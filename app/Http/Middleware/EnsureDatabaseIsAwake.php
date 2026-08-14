<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureDatabaseIsAwake
{
    public function handle(Request $request, Closure $next): Response
    {
        $maxAttempts = 5;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                DB::select('select 1');
                return $next($request);
            } catch (\Throwable $e) {
                DB::purge();

                if ($attempt === $maxAttempts) {
                    return response(
                        '<html><body style="font-family:sans-serif;text-align:center;padding-top:100px;">'
                        .'<h2>Waking up the database…</h2>'
                        .'<p>This only happens after a period of inactivity. Please refresh in a few seconds.</p>'
                        .'</body></html>',
                        503
                    );
                }

                usleep(400000 * $attempt);
            }
        }

        return $next($request);
    }
}