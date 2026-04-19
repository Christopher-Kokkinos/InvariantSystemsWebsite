<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoopbackOnlyAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = (string) $request->ip();
        $host = strtolower((string) $request->getHost());

        $allowedIps = ['127.0.0.1', '::1'];
        $allowedHosts = ['127.0.0.1', 'localhost', '::1', '[::1]'];

        if (!in_array($ip, $allowedIps, true) || !in_array($host, $allowedHosts, true)) {
            abort(403, 'Loopback-only access enforced for this internal module.');
        }

        return $next($request);
    }
}
