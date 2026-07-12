<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use App\Services\WorkspaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $abilities = $this->resolveTokenAbilities($user, $request->input('abilities', []));

        $token = $user->createToken($request->device_name ?? 'api-token', $abilities)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'abilities' => $abilities,
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'currency' => $request->currency ?? 'DZD',
            'timezone' => $request->timezone ?? 'Africa/Algiers',
        ]);

        app(WorkspaceService::class)->createForUser($user);

        $defaultAbilities = array_keys(config('api-abilities'));
        $abilities = $this->resolveTokenAbilities($user, $request->input('abilities', $defaultAbilities));

        $token = $user->createToken('api-token', $abilities)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'abilities' => $abilities,
        ], 201);
    }

    protected function resolveTokenAbilities(User $user, array $requested): array
    {
        $configuredAbilities = array_values(array_filter(array_keys(config('api-abilities')), fn (string $ability): bool => $ability !== '*'));

        if ($user->hasRole('super_admin')) {
            return in_array('*', $requested, true) ? ['*'] : array_values(array_intersect($requested, $configuredAbilities));
        }

        $allowedAbilities = $this->resolveAllowedAbilities($user);

        if (in_array('*', $requested, true)) {
            return [];
        }

        if (empty($requested)) {
            return $allowedAbilities;
        }

        return array_values(array_intersect($requested, $allowedAbilities));
    }

    protected function resolveAllowedAbilities(User $user): array
    {
        $abilityPermissions = [
            'income:read' => ['income.view'],
            'income:write' => ['income.create', 'income.update', 'income.delete', 'income.restore', 'income.archive', 'income.force-delete'],
            'expense:read' => ['expense.view'],
            'expense:write' => ['expense.create', 'expense.update', 'expense.delete', 'expense.restore', 'expense.archive', 'expense.force-delete'],
            'debt:read' => ['debt.view'],
            'debt:write' => ['debt.create', 'debt.update', 'debt.delete', 'debt.restore', 'debt.archive', 'debt.force-delete'],
            'asset:read' => ['asset.view'],
            'asset:write' => ['asset.create', 'asset.update', 'asset.delete', 'asset.restore', 'asset.archive', 'asset.force-delete'],
            'budget:read' => ['budget.view'],
            'budget:write' => ['budget.create', 'budget.update', 'budget.delete', 'budget.restore', 'budget.archive', 'budget.force-delete'],
            'goal:read' => ['goal.view'],
            'goal:write' => ['goal.create', 'goal.update', 'goal.delete', 'goal.restore', 'goal.archive', 'goal.force-delete'],
            'income-categories:read' => ['income_category.view', 'income.view'],
            'income-categories:write' => ['income_category.create', 'income_category.update', 'income_category.delete'],
            'expense-categories:read' => ['expense_category.view', 'expense.view'],
            'expense-categories:write' => ['expense_category.create', 'expense_category.update', 'expense_category.delete'],
            'zakat:read' => ['zakat.view'],
            'zakat:write' => ['zakat.create', 'zakat.update', 'zakat.delete'],
            'report:read' => ['report.view'],
            'report:write' => ['report.export', 'report.create'],
            'export:data' => ['report.export'],
            'workspace:read' => ['workspace.view', 'workspace-setting.view', 'tenant.view'],
            'workspace:write' => ['workspace-setting.update', 'workspace-user.invite', 'tenant.create', 'tenant.delete'],
            'transaction:read' => ['transaction.view'],
            'dashboard:read' => ['dashboard.view', 'platform-dashboard.view'],
            'subscription:read' => ['subscription.view', 'payment.view'],
            'subscription:write' => ['subscription.update', 'payment.verify'],
            'notification:read' => ['notification.view'],
            'notification:write' => ['notification.update', 'notification.delete'],
            'user:read' => ['platform-user.view'],
            'user:write' => ['platform-user.update'],
        ];

        $allowedAbilities = [];

        foreach ($abilityPermissions as $ability => $permissions) {
            if ($this->userHasAnyPermission($user, $permissions)) {
                $allowedAbilities[] = $ability;
            }
        }

        return array_values(array_unique($allowedAbilities));
    }

    protected function userHasAnyPermission(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('workspaces'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => __('auth.logout_success')]);
    }
}
