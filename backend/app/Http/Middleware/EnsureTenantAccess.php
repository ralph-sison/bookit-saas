<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    /**
     * @param  string  ...$roles  Allowed roles (empty = any role)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $tenant = app('current_tenant');
        $user = $request->user();

        if (! $user || ! $tenant) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        if (! $user->belongsToTenant($tenant)) {
            return response()->json([
                'message' => 'You do not have access to this business.',
            ], 403);
        }

        if (! empty($roles)) {
            $userRole = $user->roleInTenant($tenant);

            if (! in_array($userRole, $roles)) {
                return response()->json([
                    'message' => 'You do not have the required roles for this action.',
                ], 403);
            }
        }

        return $next($request);
    }
}
