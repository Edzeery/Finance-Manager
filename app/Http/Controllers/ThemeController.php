<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function switch(Request $request)
    {
        $theme = $request->input('theme', 'light');
        if (in_array($theme, ['light', 'dark'])) {
            session(['theme' => $theme]);
            if (auth()->check()) {
                auth()->user()->update(['theme' => $theme]);
            }
        }
        return response()->json(['success' => true]);
    }
}
