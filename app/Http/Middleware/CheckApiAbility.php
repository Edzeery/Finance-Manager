<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckApiAbility
{
    // Custom controller methods (non-CRUD) need explicit ability mapping.
    // CRUD methods (index/show/ store/update/destroy) are handled automatically.
    protected static array $customMethodAbilities = [
        'subscription.plans' => 'subscription:read',
        'subscription.current' => 'subscription:read',
        'subscription.changePlan' => 'subscription:write',
        'subscription.cancel' => 'subscription:write',
        'subscription.validateCoupon' => 'subscription:read',
        'workspace.switch' => 'workspace:write',
    ];

    protected static array $crudReadMethods = ['index', 'show'];
    protected static array $crudWriteMethods = ['store', 'update', 'destroy'];

    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $action = $route?->getActionMethod();
        $controller = $route?->getControllerClass();

        $ability = $this->resolveAbility($controller, $action);

        if ($ability === null) {
            Log::warning('CheckApiAbility: unmapped route', [
                'controller' => $controller,
                'method' => $action,
                'user_id' => $request->user()?->id,
            ]);

            return response()->json([
                'message' => __('messages.unauthorized'),
                'error' => 'unmapped_route',
            ], 403);
        }

        $user = $request->user();

        if ($user->tokenCan('*')) {
            return $next($request);
        }

        if (!$user->tokenCan($ability)) {
            return response()->json([
                'message' => __('messages.unauthorized'),
                'required_ability' => $ability,
            ], 403);
        }

        return $next($request);
    }

    protected function resolveAbility(?string $controller, ?string $method): ?string
    {
        if ($controller === null || $method === null) {
            return null;
        }

        $resource = $this->resolveResource($controller);
        if ($resource === null) {
            return null;
        }

        $key = "{$resource}.{$method}";

        if (isset(static::$customMethodAbilities[$key])) {
            return static::$customMethodAbilities[$key];
        }

        if (in_array($method, static::$crudReadMethods)) {
            return "{$resource}:read";
        }

        if (in_array($method, static::$crudWriteMethods)) {
            return "{$resource}:write";
        }

        return null;
    }

    protected function resolveResource(?string $controller): ?string
    {
        if ($controller === null) {
            return null;
        }

        $parts = explode('\\', $controller);
        $class = end($parts);
        $resource = str_replace('Controller', '', $class);

        return lcfirst($resource);
    }

    public static function getRouteAbilityMap(): array
    {
        return [];
    }
}
