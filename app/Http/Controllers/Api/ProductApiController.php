<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductApiController extends Controller
{
    /**
     * Search the catalog. This is the "tool" the AI assistant calls when a
     * shopper asks about products (e.g. "red shirts under $40").
     *
     * Every filter is optional — the assistant fills in only what it knows.
     */
    public function search(Request $request)
    {
        // The LLM sends free-form casing ("Women", "RED", "m"). The DB stores
        // values in fixed casing and matches case-sensitively, so normalize
        // each filter to how it's actually stored before querying.
        if ($request->filled('gender')) {
            $request->merge(['gender' => strtolower($request->input('gender'))]);
        }
        if ($request->filled('color')) {
            $request->merge(['color' => ucwords(strtolower($request->input('color')))]);
        }
        if ($request->filled('size')) {
            $request->merge(['size' => strtoupper($request->input('size'))]);
        }

        $validated = $request->validate([
            'q'         => ['nullable', 'string', 'max:100'],
            'category'  => ['nullable', 'string', 'max:50'],
            'gender'    => ['nullable', 'string', 'in:men,women'],
            'color'     => ['nullable', 'string', 'max:30'],
            'size'      => ['nullable', 'string', 'max:10'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'in_stock'  => ['nullable', 'boolean'],
            'limit'     => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $limit = $validated['limit'] ?? 10;

        $products = Product::query()
            ->when($validated['q'] ?? null, fn ($query, $q) => $query->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
                ->orWhere('category', 'like', "%{$q}%")
            ))
            ->when($validated['category'] ?? null, fn ($query, $c) => $query->where('category', 'like', "%{$c}%"))
            ->when($validated['gender'] ?? null, fn ($query, $g) => $query->whereRaw('LOWER(gender) = ?', [$g]))
            ->when($validated['color'] ?? null, fn ($query, $color) => $query->whereJsonContains('colors', $color))
            ->when($validated['size'] ?? null, fn ($query, $size) => $query->whereJsonContains('sizes', $size))
            ->when($validated['min_price'] ?? null, fn ($query, $min) => $query->where('price', '>=', $min))
            ->when($validated['max_price'] ?? null, fn ($query, $max) => $query->where('price', '<=', $max))
            ->when(($validated['in_stock'] ?? false), fn ($query) => $query->where('stock', '>', 0))
            ->latest()
            ->take($limit)
            ->get();

        return response()->json([
            'count'   => $products->count(),
            'results' => $products->map(fn ($product) => $this->transform($product)),
        ]);
    }

    /**
     * Look up one product by its slug — the "check stock / get details" tool.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->first();

        if (! $product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        return response()->json($this->transform($product, full: true));
    }

    /**
     * Shape a product into a small, LLM-friendly payload. We keep it lean so
     * the assistant spends fewer tokens and gets only what it needs to answer.
     */
    private function transform(Product $product, bool $full = false): array
    {
        $data = [
            'name'      => $product->name,
            'price'     => (float) $product->price,
            'category'  => $product->category,
            'gender'    => $product->gender,
            'colors'    => $product->colors ?? [],
            'sizes'     => $product->sizes ?? [],
            'stock'     => $product->stock,
            'in_stock'  => $product->stock > 0,
            'url'       => route('shop.show', $product->slug),
            'image_url' => $product->image ? asset('storage/' . $product->image) : null,
        ];

        $data['description'] = $full
            ? $product->description
            : Str::limit($product->description, 160);

        return $data;
    }
}