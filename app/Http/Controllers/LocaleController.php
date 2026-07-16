<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(string $locale, Request $request)
    {
        if (in_array($locale, ['ar', 'fr', 'en'])) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
            if (auth()->check()) {
                auth()->user()->update(['locale' => $locale]);
            }
        }

        return redirect()->back();
    }
}
