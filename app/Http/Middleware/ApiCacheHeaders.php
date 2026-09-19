<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiCacheHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->is('api/admin/*')) {
            $response->headers->set('Cache-Control', 'no-store, private');
        } elseif ($request->is('api/portfolio') || $request->is('api/projects*')) {
            $response->headers->set('Cache-Control', 'no-cache');
        }

        return $response;
    }
}
