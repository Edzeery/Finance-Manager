<?php

namespace App\Http\Controllers\Account;

use App\Concerns\ParsesUserAgent;
use App\Helpers\CurrencyHelper;
use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    use ParsesUserAgent;

    private const TABS = ['profile', 'security'];

    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function index()
    {
        $user = auth()->user();
        $tab = request()->query('tab', 'profile');
        if (! in_array($tab, self::TABS)) {
            $tab = 'profile';
        }

        $data = match ($tab) {
            'security' => $this->securityData($user),
            default => [],
        };

        $initials = implode('', array_map(fn ($n) => $n[0] ?? '', array_filter(explode(' ', $user->name))));

        return view('account.settings', array_merge(compact('user', 'initials', 'tab'), $data));
    }

    private function securityData($user): array
    {
        $currentSessionId = session()->getId();

        // Active sessions from sessions table
        $activeSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get();

        $activeUserAgents = $activeSessions->pluck('user_agent')->filter()->unique()->values()->all();
        $inactiveThreshold = now()->subMinutes(15)->timestamp;

        // Build unified session list with status
        $allSessions = collect();
        $currentSessionFound = false;

        foreach ($activeSessions as $session) {
            $payload = json_decode($session->payload, true);
            $loginAt = $payload['login_at'] ?? $payload['_login_at'] ?? null;
            $isActive = $session->last_activity >= $inactiveThreshold;
            $isCurrent = $session->id === $currentSessionId;

            if ($isCurrent) {
                $currentSessionFound = true;
            }

            $allSessions->push((object) [
                'id' => $session->id,
                'ip_address' => $session->ip_address,
                'user_agent' => $session->user_agent,
                'last_activity' => $session->last_activity,
                'is_current' => $isCurrent,
                'device' => $this->parseDevice($session->user_agent),
                'browser' => $this->parseBrowser($session->user_agent),
                'os' => $this->parseOS($session->user_agent),
                'login_at' => $loginAt ? Carbon::parse($loginAt) : null,
                'status' => $isActive ? 'active' : 'inactive',
                'sort_key' => $session->last_activity,
            ]);
        }

        // Race condition: current session not in DB yet — add it manually
        if (! $currentSessionFound) {
            $ua = request()->userAgent() ?? '';
            $allSessions->push((object) [
                'id' => $currentSessionId,
                'ip_address' => request()->ip(),
                'user_agent' => $ua,
                'last_activity' => now()->timestamp,
                'is_current' => true,
                'device' => $this->parseDevice($ua),
                'browser' => $this->parseBrowser($ua),
                'os' => $this->parseOS($ua),
                'login_at' => session()->has('login_at') ? Carbon::parse(session('login_at')) : now(),
                'status' => 'active',
                'sort_key' => now()->timestamp,
            ]);
        }

        // Successful logins without a matching active session (ended sessions)
        $endedAttempts = LoginAttempt::where('user_id', $user->id)
            ->where('status', 'success')
            ->orderByDesc('created_at')
            ->get()
            ->filter(fn ($attempt) => ! in_array($attempt->user_agent, $activeUserAgents));

        foreach ($endedAttempts as $attempt) {
            $allSessions->push((object) [
                'id' => null,
                'ip_address' => $attempt->ip_address,
                'user_agent' => $attempt->user_agent,
                'last_activity' => null,
                'is_current' => false,
                'device' => $attempt->device,
                'browser' => $attempt->browser,
                'os' => $attempt->os,
                'login_at' => $attempt->created_at,
                'status' => 'logged_out',
                'sort_key' => $attempt->created_at?->timestamp ?? 0,
            ]);
        }

        // Sort deterministically: by sort_key desc, then by id as tiebreaker
        $allSessions = $allSessions
            ->sortByDesc(fn ($s) => $s->sort_key)
            ->sortByDesc(fn ($s) => $s->is_current ? 1 : 0)
            ->values();

        return compact('allSessions');
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

        return redirect()->route('settings.account.index', ['tab' => 'profile'])
            ->with('success', __('messages.settings_saved'));
    }

    public function revokeSession(Request $request, string $sessionId)
    {
        $user = auth()->user();
        $currentSessionId = session()->getId();

        if ($sessionId === $currentSessionId) {
            return back()->withErrors(['session' => __('settings.cannot_revoke_current')]);
        }

        // Get session info before deleting
        $session = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', $sessionId)
            ->first();

        $deleted = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', $sessionId)
            ->delete();

        if (! $deleted) {
            return back()->withErrors(['session' => __('settings.session_not_found')]);
        }

        // Notify the user about the revoked session
        $sessionInfo = $session
            ? ($this->parseDevice($session->user_agent).' — '.$this->parseBrowser($session->user_agent).' — '.$this->parseOS($session->user_agent))
            : $sessionId;

        $this->notificationService->sessionRevoked($user->id, $sessionInfo);

        return back()->with('success', __('settings.session_revoked'));
    }

    public function revokeAllSessions()
    {
        $user = auth()->user();
        $currentSessionId = session()->getId();

        $count = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        $this->notificationService->sessionRevoked(
            $user->id,
            __('notifications.all_sessions_revoked_count', ['count' => $count])
        );

        return back()->with('success', __('settings.all_sessions_revoked'));
    }
}
