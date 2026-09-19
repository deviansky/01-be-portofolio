<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->query('lang');
        if (! in_array($lang, ['id', 'en'], true)) {
            $acceptLang = $request->header('Accept-Language', '');
            $lang = str_starts_with(strtolower($acceptLang), 'en') ? 'en' : 'id';
        }

        app()->setLocale($lang);

        $response = $next($request);

        if ($request->is('api/*') && ! $request->is('api/admin/*')) {
            $response->headers->set('Vary', 'Accept-Language');
        }

        return $response;
    }
}
