<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the public chatbot API. Callers (Make, n8n, Voiceflow, etc.) must
 * send the shared secret in an "X-Api-Key" header that matches CHATBOT_API_KEY.
 */
class VerifyApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $configured = config('services.chatbot.key');

        // Fail loudly if the server forgot to set the key, rather than
        // silently leaving the endpoint open.
        if (empty($configured)) {
            return response()->json([
                'error' => 'Chatbot API key is not configured on the server.',
            ], 503);
        }

        $provided = $request->header('X-Api-Key');

        if (! $provided || ! hash_equals($configured, $provided)) {
            return response()->json([
                'error' => 'Invalid or missing API key.',
            ], 401);
        }

        return $next($request);
    }
}
