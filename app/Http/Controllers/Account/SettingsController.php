<?php

namespace App\Http\Controllers\Account;

use App\Helpers\CurrencyHelper;
use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $initials = implode('', array_map(fn ($n) => $n[0] ?? '', array_filter(explode(' ', $user->name))));

        $currentSessionId = session()->getId();

        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                $payload = json_decode($session->payload, true);
                $loginAt = $payload['login_at'] ?? $payload['_login_at'] ?? null;

                return (object) [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => $session->last_activity,
                    'is_current' => $session->id === $currentSessionId,
                    'device' => $this->parseDevice($session->user_agent),
                    'browser' => $this->parseBrowser($session->user_agent),
                    'os' => $this->parseOS($session->user_agent),
                    'login_at' => $loginAt ? \Carbon\Carbon::parse($loginAt) : null,
                ];
            });

        $loginHistory = LoginAttempt::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->map(fn ($attempt) => (object) [
                'status' => $attempt->status,
                'ip_address' => $attempt->ip_address,
                'device' => $attempt->device,
                'browser' => $attempt->browser,
                'os' => $attempt->os,
                'suspicious' => $attempt->suspicious,
                'created_at' => $attempt->created_at,
            ]);

        return view('account.settings', compact('user', 'initials', 'sessions', 'loginHistory'));
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

    public function revokeSession(Request $request, string $sessionId)
    {
        $user = auth()->user();
        $currentSessionId = session()->getId();

        if ($sessionId === $currentSessionId) {
            return back()->withErrors(['session' => __('settings.cannot_revoke_current')]);
        }

        $deleted = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', $sessionId)
            ->delete();

        if (! $deleted) {
            return back()->withErrors(['session' => __('settings.session_not_found')]);
        }

        return back()->with('success', __('settings.session_revoked'));
    }

    public function revokeAllSessions()
    {
        $user = auth()->user();
        $currentSessionId = session()->getId();

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        return back()->with('success', __('settings.all_sessions_revoked'));
    }

    private function parseDevice(string $userAgent): string
    {
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent)) {
            return preg_match('/iPad|iPod/i', $userAgent) ? 'tablet' : 'phone';
        }
        return 'desktop';
    }

    private function parseBrowser(string $userAgent): string
    {
        $browsers = [
            'Edge' => 'Edge',
            'Edg/' => 'Edge',
            'OPR' => 'Opera',
            'Chrome' => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari' => 'Safari',
            'MSIE' => 'IE',
            'Trident' => 'IE',
        ];
        foreach ($browsers as $pattern => $name) {
            if (stripos($userAgent, $pattern) !== false) {
                return $name;
            }
        }
        return 'Unknown';
    }

    private function parseOS(string $userAgent): string
    {
        $oses = [
            'Windows NT 10' => 'Windows 10/11',
            'Windows NT 6.3' => 'Windows 8.1',
            'Windows NT 6.2' => 'Windows 8',
            'Windows NT 6.1' => 'Windows 7',
            'Windows' => 'Windows',
            'Mac OS X' => 'macOS',
            'Android' => 'Android',
            'iPhone OS' => 'iOS',
            'iPad' => 'iPadOS',
            'Linux' => 'Linux',
            'CrOS' => 'ChromeOS',
        ];
        foreach ($oses as $pattern => $name) {
            if (stripos($userAgent, $pattern) !== false) {
                return $name;
            }
        }
        return 'Unknown';
    }
}
