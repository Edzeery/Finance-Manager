<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $theme = auth()->user()->theme;
            $currency = auth()->user()->currency;
        } else {
            $theme = $request->session()->get('theme', config('app.theme', 'light'));
            $currency = $request->session()->get('currency', config('finance.currency'));
        }

        if (! in_array($theme, ['light', 'dark'])) {
            $theme = 'light';
        }

        config(['app.theme' => $theme]);
        view()->share('theme', $theme);

        $allowedCurrencies = array_keys(config('finance.currencies', ['DZD' => [], 'USD' => [], 'EUR' => []]));
        if (! $currency || ! in_array($currency, $allowedCurrencies)) {
            $currency = config('finance.currency', 'DZD');
        }

        config(['finance.currency' => $currency]);
        config(['finance.currency_symbol' => config("finance.currencies.{$currency}.symbol", 'د.ج')]);

        return $next($request);
    }
}
