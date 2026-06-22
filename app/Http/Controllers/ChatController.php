<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    /**
     * The assistant's standing instructions — same idea as the n8n System Message.
     */
    private string $systemPrompt = <<<'PROMPT'
        You are Jeltro's friendly shopping assistant. Jeltro is an online custom apparel store.
        Use the search_products tool to answer questions about products, prices, colors, sizes or availability.
        Only pass the filters the user actually mentions. Call the tool once, then answer from the results.
        Keep answers short and friendly. When you mention a product, link it as a Markdown link using
        the product name as the text, e.g. [Cargo Shorts](https://...). Never paste raw URLs.
        If nothing matches, say so honestly. Only talk about Jeltro and its products.
        PROMPT;

    /**
     * Chat endpoint hit by the website widget. Takes the user's message plus the
     * recent history (for context) and returns the assistant's reply.
     */
    public function message(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['nullable', 'array', 'max:10'],
        ]);

        $messages = [['role' => 'system', 'content' => $this->systemPrompt]];

        // Replay recent turns so the model has conversational memory.
        foreach ($validated['history'] ?? [] as $turn) {
            if (isset($turn['role'], $turn['content']) && in_array($turn['role'], ['user', 'assistant'], true)) {
                $messages[] = ['role' => $turn['role'], 'content' => (string) $turn['content']];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $validated['message']];

        return response()->json(['reply' => $this->runConversation($messages)]);
    }

    /**
     * The tool-calling loop: keep asking Groq until it stops requesting tools,
     * then return its final text answer. This is what the n8n AI Agent did for us.
     */
    private function runConversation(array $messages): string
    {
        $rounds  = 0; // tool-call rounds (safety cap so it can't loop forever)
        $retries = 0; // retries for transient model glitches

        while ($rounds < 5 && $retries < 8) {
            $response = Http::withToken(config('services.groq.key'))
                ->timeout(30)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => config('services.groq.model'),
                    'messages'    => $messages,
                    'tools'       => $this->tools(),
                    'tool_choice' => 'auto',
                ]);

            if ($response->failed()) {
                // Llama on Groq sometimes emits a malformed tool call
                // ("tool_use_failed"). It's transient — a fresh sample usually
                // fixes it, so retry before giving up.
                if ($response->json('error.code') === 'tool_use_failed') {
                    $retries++;
                    continue;
                }
                report(new \RuntimeException('Groq API error: ' . $response->body()));
                return "Sorry, I'm having trouble right now. Please try again in a moment.";
            }

            $choice = $response->json('choices.0.message');

            // No tool calls → this is the final answer.
            if (empty($choice['tool_calls'])) {
                return $choice['content'] ?? "Sorry, I didn't catch that.";
            }

            // Record the model's tool request, run each tool, feed results back.
            $messages[] = [
                'role'       => 'assistant',
                'content'    => $choice['content'] ?? '',
                'tool_calls' => $choice['tool_calls'],
            ];

            foreach ($choice['tool_calls'] as $call) {
                $args = json_decode($call['function']['arguments'] ?? '{}', true) ?: [];

                $messages[] = [
                    'role'         => 'tool',
                    'tool_call_id' => $call['id'],
                    'content'      => json_encode($this->searchProducts($args)),
                ];
            }

            $rounds++;
        }

        return "Sorry, I couldn't complete that request. Could you rephrase?";
    }

    /**
     * The tool schema we expose to the LLM (Groq/OpenAI function-calling format).
     */
    private function tools(): array
    {
        return [[
            'type'     => 'function',
            'function' => [
                'name'        => 'search_products',
                'description' => 'Search the Jeltro clothing catalog. Use whenever the user asks about products, prices, colors, sizes or availability.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'q'         => ['type' => 'string', 'description' => 'Keywords like hoodie, joggers, tee'],
                        'gender'    => ['type' => 'string', 'enum' => ['men', 'women'], 'description' => 'Target gender'],
                        'color'     => ['type' => 'string', 'description' => 'A single color, e.g. red, black'],
                        'size'      => ['type' => 'string', 'description' => 'A size like S, M, L'],
                        'max_price' => ['type' => 'number', 'description' => 'Maximum price'],
                        'in_stock'  => ['type' => 'boolean', 'description' => 'Only show items currently in stock'],
                    ],
                ],
            ],
        ]];
    }

    /**
     * The actual tool implementation: query the catalog. Mirrors /api/products,
     * with the same case-handling for the case-sensitive DB columns.
     */
    private function searchProducts(array $f): array
    {
        $products = Product::query()
            ->when(! empty($f['q']), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%' . $f['q'] . '%')
                ->orWhere('description', 'like', '%' . $f['q'] . '%')
                ->orWhere('category', 'like', '%' . $f['q'] . '%')))
            ->when(! empty($f['gender']), fn ($q) => $q->whereRaw('LOWER(gender) = ?', [strtolower($f['gender'])]))
            ->when(! empty($f['color']), fn ($q) => $q->whereJsonContains('colors', ucwords(strtolower($f['color']))))
            ->when(! empty($f['size']), fn ($q) => $q->whereJsonContains('sizes', strtoupper($f['size'])))
            ->when(! empty($f['max_price']), fn ($q) => $q->where('price', '<=', $f['max_price']))
            ->when(! empty($f['in_stock']), fn ($q) => $q->where('stock', '>', 0))
            ->latest()
            ->take(5)
            ->get();

        return [
            'count'   => $products->count(),
            'results' => $products->map(fn ($p) => [
                'name'     => $p->name,
                'price'    => (float) $p->price,
                'gender'   => $p->gender,
                'colors'   => $p->colors ?? [],
                'sizes'    => $p->sizes ?? [],
                'in_stock' => $p->stock > 0,
                'url'      => route('shop.show', $p->slug),
            ])->all(),
        ];
    }
}
