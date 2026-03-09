<?php

declare(strict_types=1);

namespace Tests\Helpers;

use App\Domain\Auth\Models\User;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Support\Str;

trait AuthHelper
{
    protected function createTenantWithOwner(array $tenantData = [], array $userData = []): array
    {
        $tenant = Tenant::factory()->create($tenantData);
        $user = User::factory()->create($userData);

        $tenant->users()->attach($user->id, [
            'id' => Str::uuid()->toString(),
            'role' => 'owner',
            'is_default' => true,
            'joined_at' => now(),
        ]);

        return ['tenant' => $tenant, 'user' => $user];
    }

    protected function createUserInTenant(Tenant $tenant, string $role = 'staff', array $userData = []): User
    {
        $user = User::factory()->create($userData);

        $tenant->users()->attach($user->id, [
            'id' => Str::uuid()->toString(),
            'role' => $role,
            'is_default' => true,
            'joined_at' => now(),
        ]);

        return $user;
    }

    protected function authHeaders(User $user): array
    {
        $token = $user->createToken('test-token')->plainTextToken;

        return [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ];
    }

    protected function tenantHeaders(User $user, Tenant $tenant): array
    {
        return array_merge($this->authHeaders($user), [
            'X-Tenant-ID' => $tenant->id,
        ]);
    }
}
