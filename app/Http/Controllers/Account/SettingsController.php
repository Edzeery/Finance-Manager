<?php

namespace App\Http\Controllers\Account;

use App\Helpers\CurrencyHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $initials = implode('', array_map(fn($n) => $n[0] ?? '', array_filter(explode(' ', $user->name))));

        return view('account.settings', compact('user', 'initials'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'language' => ['nullable', 'in:ar,fr,en'],
            'currency' => ['nullable', Rule::in(CurrencyHelper::availableCurrencyCodes() ?: ['DZD', 'USD', 'EUR'])],
            'timezone' => ['nullable', 'string', 'max:100'],
        ]);

        $user = auth()->user();

        if ($request->filled('language')) {
            $user->locale = $request->language;
            session(['locale' => $request->language]);
            app()->setLocale($request->language);
        }

        if ($request->filled('currency')) {
            $user->currency = $request->currency;
            session(['currency' => $request->currency]);
        }

        if ($request->filled('timezone')) {
            $user->timezone = $request->timezone;
            session(['timezone' => $request->timezone]);
        }

        $user->save();

        return redirect()->route('account.settings')
            ->with('success', __('messages.settings_saved'));
    }
}
