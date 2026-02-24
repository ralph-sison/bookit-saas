<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Tenant\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolveTenant($request);

        if (! $tenant) {
            return response()->json([
                'message' => 'Tenant could not be resolved. Provide X-Tenant-ID header, use a tenant subdomain, or pass ?tenant=slug.'
            ], 404);
        }

        if (! $tenant->isActive()) {
            return response()->json([
                'message' => 'This business account is inactive.'
            ], 403);
        }

        // Bind tenant to the container so it's available everywhere
        app()->instance('current_tenant', $tenant);

        // ALso store on the request
        $request->merge(['tenant' => $tenant]);

        return $next($request);
    }

    private function resolveTenant(Request $request): ?Tenant
    {
        // 1. Header-based (highest priority for API)
        if ($tenantId = $request->header('X-Tenant-ID')) {
            return Tenant::find($tenantId);
        }

        // 2. Subdomain-based
        $host = $request->getHost();
        $baseDomain = config('app.base_domain', 'bookit.com');
        if (str_ends_with($host, ".{$baseDomain}")) {
            $slug = str_replace(".{$baseDomain}", '', $host);

            return Tenant::bySlug($slug)->first();
        }

        // 3. Query param (dev convenience)
        if ($slug = $request->query('tenant')) {
            return Tenant::bySlug($slug)->first();
        }

        // 4. If user is authenticated, use their default tenant
        if ($request->user()) {
            return $request->user()->defaultTenant();
        }

        return null;
    }
}
