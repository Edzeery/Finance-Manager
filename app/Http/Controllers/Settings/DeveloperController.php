<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ApiUsageLog;
use App\Services\ApiQuotaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class DeveloperController extends Controller
{
    public function index(ApiQuotaService $quotaService)
    {
        $user = auth()->user();
        $allTokens = $user->tokens()->orderBy('created_at', 'desc')->get();

        $tokens = $allTokens->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'abilities' => $t->abilities,
            'last_used_at' => $t->last_used_at,
            'created_at' => $t->created_at,
            'expires_at' => $t->expires_at,
            'deactivated_at' => $t->deactivated_at,
            'plaintext_token' => $t->plaintext_token ? Crypt::decryptString($t->plaintext_token) : null,
        ]);

        $now = now();
        $stats = [
            'total' => $tokens->count(),
            'active' => $tokens->filter(fn ($t) => ! $t['expires_at'] || $t['expires_at'] > $now)->filter(fn ($t) => ! $t['deactivated_at'])->count(),
            'expired' => $tokens->filter(fn ($t) => $t['expires_at'] && $t['expires_at'] <= $now)->count(),
            'never_used' => $tokens->filter(fn ($t) => ! $t['last_used_at'])->count(),
        ];

        $subscription = $user->activeSubscription();
        $plan = $subscription?->plan;
        $maxTokens = (int) ($plan?->getFeatureValue('max_api_tokens') ?? 5);
        $stats['token_limit'] = $maxTokens;

        $quotaLimits = $quotaService->getLimits($plan);
        $quotaUsage = $quotaService->getUsage($user->id);
        $quotaReset = $quotaService->getResetTimes();

        $sevenDaysAgo = now()->subDays(7);

        $usageHistory = ApiUsageLog::where('user_id', $user->id)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $tokenUsage = ApiUsageLog::where('user_id', $user->id)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->selectRaw('token_id, COUNT(*) as count')
            ->groupBy('token_id')
            ->get()
            ->keyBy('token_id')
            ->map(fn ($item) => $item->count);

        $tokens = $tokens->map(function ($token) use ($tokenUsage) {
            $token['usage_7d'] = $tokenUsage[$token['id']] ?? 0;

            return $token;
        });

        return view('settings.developer', [
            'tokens' => $tokens,
            'stats' => $stats,
            'abilities' => config('api-abilities'),
            'token' => session('api_token'),
            'quotaLimits' => $quotaLimits,
            'quotaUsage' => $quotaUsage,
            'quotaReset' => $quotaReset,
            'usageHistory' => $usageHistory,
            'totalRequests' => $tokenUsage->sum(),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $subscription = $user->activeSubscription();
        $plan = $subscription?->plan;
        $maxTokens = (int) ($plan?->getFeatureValue('max_api_tokens') ?? 5);
        $currentCount = $user->tokens()->whereNull('deactivated_at')->count();

        if ($currentCount >= $maxTokens) {
            return redirect()->route('account.settings.developer')
                ->with('error', __('developer.token_limit_reached', ['limit' => $maxTokens]));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abilities' => ['required', 'array', 'min:1'],
            'abilities.*' => ['string', 'in:'.implode(',', array_keys(config('api-abilities')))],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $expiresAt = ! empty($validated['expires_at']) ? Carbon::parse($validated['expires_at']) : null;

        $newToken = $user->createToken(
            $validated['name'],
            $validated['abilities'],
            $expiresAt,
        );

        $tokenModel = $newToken->accessToken;
        $tokenModel->plaintext_token = Crypt::encryptString($newToken->plainTextToken);
        $tokenModel->save();

        return redirect()->route('account.settings.developer')
            ->with('api_token', $newToken->plainTextToken)
            ->with('success', __('developer.token_created'));
    }

    public function show(Request $request, $tokenId)
    {
        $request->validate(['password' => 'required|string']);

        $user = Auth::user();

        if (! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => __('developer.invalid_password')], 403);
        }

        $token = PersonalAccessToken::findOrFail($tokenId);

        if ($token->tokenable_id !== $user->id) {
            abort(403);
        }

        if (! $token->plaintext_token) {
            return response()->json(['message' => __('developer.token_not_available')], 404);
        }

        $plainText = Crypt::decryptString($token->plaintext_token);

        return response()->json([
            'token' => $plainText,
            'name' => $token->name,
            'abilities' => $token->abilities,
            'created_at' => $token->created_at->format('M d, Y H:i'),
            'last_used_at' => $token->last_used_at?->diffForHumans() ?? __('developer.never_used'),
            'expires_at' => $token->expires_at?->format('M d, Y') ?? __('developer.never_expires'),
            'deactivated_at' => $token->deactivated_at?->format('M d, Y H:i'),
        ]);
    }

    public function update(Request $request, $tokenId)
    {
        $token = PersonalAccessToken::findOrFail($tokenId);

        if ($token->tokenable_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $token->update(['name' => $validated['name']]);

        return redirect()->route('account.settings.developer')
            ->with('success', __('developer.token_updated'));
    }

    public function regenerate(Request $request, $tokenId)
    {
        $token = PersonalAccessToken::findOrFail($tokenId);

        if ($token->tokenable_id !== auth()->id()) {
            abort(403);
        }

        $newToken = auth()->user()->createToken(
            $token->name,
            $token->abilities
        );

        $newTokenModel = $newToken->accessToken;
        $newTokenModel->plaintext_token = Crypt::encryptString($newToken->plainTextToken);
        $newTokenModel->save();

        $token->delete();

        return redirect()->route('account.settings.developer')
            ->with('api_token', $newToken->plainTextToken)
            ->with('success', __('developer.token_regenerated'));
    }

    public function deactivate($tokenId)
    {
        $token = PersonalAccessToken::findOrFail($tokenId);

        if ($token->tokenable_id !== auth()->id()) {
            abort(403);
        }

        $token->update(['deactivated_at' => now()]);

        return redirect()->route('account.settings.developer')
            ->with('success', __('developer.token_deactivated'));
    }

    public function activate($tokenId)
    {
        $token = PersonalAccessToken::findOrFail($tokenId);

        if ($token->tokenable_id !== auth()->id()) {
            abort(403);
        }

        $token->update(['deactivated_at' => null]);

        return redirect()->route('account.settings.developer')
            ->with('success', __('developer.token_activated'));
    }

    public function verifyPassword(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $user = Auth::user();

        $valid = Hash::check($request->password, $user->password);

        return response()->json(['valid' => $valid]);
    }

    public function destroy($tokenId)
    {
        $token = PersonalAccessToken::findOrFail($tokenId);

        if ($token->tokenable_id !== auth()->id()) {
            abort(403);
        }

        $token->delete();

        return redirect()->route('account.settings.developer')
            ->with('success', __('developer.token_revoked'));
    }

    public function destroyAll()
    {
        auth()->user()->tokens()->delete();

        return redirect()->route('account.settings.developer')
            ->with('success', __('developer.all_tokens_revoked'));
    }
}
