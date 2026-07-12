<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function switch(string $currency, Request $request)
    {
        $allowed = array_keys(config('finance.currencies', ['DZD' => [], 'USD' => [], 'EUR' => []]));

        if (in_array($currency, $allowed)) {
            session(['currency' => $currency]);
            if (auth()->check()) {
                auth()->user()->update(['currency' => $currency]);
            }
        }

        return redirect()->back();
    }
}
