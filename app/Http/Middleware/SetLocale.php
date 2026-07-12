<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $locale = auth()->user()->locale;
        } else {
            $locale = $request->session()->get('locale', config('app.locale'));
        }

        if (in_array($locale, ['ar', 'fr', 'en'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
