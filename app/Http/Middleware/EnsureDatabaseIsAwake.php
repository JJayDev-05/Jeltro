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
        $maxAttempts = 6;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                DB::select('select 1');
                break; // database is awake and responding, proceed normally
            } catch (\Throwable $e) {
                if ($attempt === $maxAttempts) {
                    throw $e; // out of retries, let it actually fail
                }
                DB::purge(); // drop the dead connection so the next attempt reconnects fresh
                usleep(500000 * $attempt); // wait a bit longer each retry: 0.5s, 1s, 1.5s...
            }
        }

        return $next($request);
    }
}