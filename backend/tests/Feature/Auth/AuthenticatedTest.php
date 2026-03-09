<?php

declare(strict_types=1);

use App\Domain\Auth\Models\User;

uses(\Tests\Helpers\AuthHelper::class);

describe('authenticated routes', function () {
    it('returns current user profile via GET /me', function () {
        ['user' => $user] = $this->createTenantWithOwner();

        $response = $this->getJson('/api/v1/auth/me', $this->authHeaders($user));

        $response->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.first_name', $user->first_name);
    });

    it('includes tenants in /me response', function () {
        ['user' => $user, 'tenant' => $tenant] = $this->createTenantWithOwner();

        $response = $this->getJson('/api/v1/auth/me', $this->authHeaders($user));

        $response->assertOk()
            ->assertJsonCount(1, 'data.tenants')
            ->assertJsonPath('data.tenants.0.name', $tenant->name);
    });

    it('logs out and revokes token', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $headers = [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ];

        $response = $this->postJson('/api/v1/auth/logout', [], $headers);
        $response->assertOk();

        // Verify token was deleted from database
        expect($user->fresh()->tokens()->count())->toBe(0);
    });

    it('rejects unauthenticated access', function () {
        $this->getJson('/api/v1/auth/me')
            ->assertStatus(401);

        $this->postJson('/api/v1/auth/logout')
            ->assertStatus(401);
    });
});
