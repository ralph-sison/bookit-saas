<?php

declare(strict_types=1);

use App\Domain\Tenant\Models\Tenant;

uses(\Tests\Helpers\AuthHelper::class);

describe('tenant resolution middleware', function () {
    // We need a route that uses the tenant middleware for testing.
    // Let's register a test route.
    beforeEach(function () {
        \Illuminate\Support\Facades\Route::middleware(['auth:sanctum', 'tenant'])
            ->get('/api/v1/test/tenant', function () {
                $tenant = app('current_tenant');

                return response()->json([
                    'tenant_id' => $tenant->id,
                    'tenant_name' => $tenant->name,
                ]);
            });
    });

    it('resolves tenant via X-Tenant-ID header', function () {
        ['tenant' => $tenant, 'user' => $user] = $this->createTenantWithOwner();

        $response = $this->getJson(
            '/api/v1/test/tenant',
            $this->tenantHeaders($user, $tenant)
        );

        $response->assertOk()
            ->assertJsonPath('tenant_id', $tenant->id);
    });

    it('resolves tenant via query param', function () {
        ['tenant' => $tenant, 'user' => $user] = $this->createTenantWithOwner();

        $response = $this->getJson(
            "/api/v1/test/tenant?tenant={$tenant->slug}",
            $this->authHeaders($user)
        );

        $response->assertOk()
            ->assertJsonPath('tenant_id', $tenant->id);
    });

    it('resolves to user default tenant when no tenant specified', function () {
        ['tenant' => $tenant, 'user' => $user] = $this->createTenantWithOwner();

        $response = $this->getJson(
            '/api/v1/test/tenant',
            $this->authHeaders($user)
        );

        $response->assertOk()
            ->assertJsonPath('tenant_id', $tenant->id);
    });

    it('returns 404 for non-existent tenant', function () {
        ['user' => $user] = $this->createTenantWithOwner();

        $response = $this->getJson(
            '/api/v1/test/tenant',
            array_merge($this->authHeaders($user), [
                'X-Tenant-ID' => '00000000-0000-0000-0000-000000000000',
            ])
        );

        $response->assertStatus(404);
    });

    it('returns 403 for inactive tenant', function () {
        ['user' => $user] = $this->createTenantWithOwner(
            tenantData: ['is_active' => false]
        );

        $response = $this->getJson(
            '/api/v1/test/tenant',
            $this->authHeaders($user)
        );

        $response->assertStatus(403);
    });

});
