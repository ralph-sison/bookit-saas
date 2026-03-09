<?php

declare(strict_types=1);

use App\Domain\Auth\Models\User;
use App\Domain\Tenant\Models\Tenant;

uses(\Tests\Helpers\AuthHelper::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});

describe('POST /api/v1/auth/register', function () {
    it('registers a new tenant and owner', function () {
        $response = $this->postJson('/api/v1/auth/register', [
            'business_name' => 'Acme Salon',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@acme.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => ['id', 'first_name', 'last_name', 'email'],
                    'token',
                    'token_type',
                ],
            ]);

        // Verify tenant was created
        $this->assertDatabaseHas('tenants', [
            'name' => 'Acme Salon',
            'subscription_status' => 'trial',
        ]);

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'email' => 'jane@acme.com',
        ]);

        // Verify pivot relationship
        $user = User::where('email', 'jane@acme.com')->first();
        $tenant = Tenant::where('name', 'Acme Salon')->first();

        expect($user->tenants)->toHaveCount(1);
        expect($user->roleInTenant($tenant))->toBe('owner');
    });

    it('generates a unique slug from business name', function () {
        $this->postJson('/api/v1/auth/register', [
            'business_name' => 'Cool Cuts',
            'first_name' => 'A',
            'last_name' => 'User',
            'email' => 'a@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->postJson('/api/v1/auth/register', [
            'business_name' => 'Cool Cuts',
            'first_name' => 'B',
            'last_name' => 'User',
            'email' => 'b@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $slugs = Tenant::pluck('slug')->toArray();

        expect($slugs)->toContain('cool-cuts');
        expect($slugs)->toContain('cool-cuts-1');
    });

    it('validates required fields', function () {
        $response = $this->postJson('/api/v1/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'business_name',
                'first_name',
                'last_name',
                'email',
                'password',
            ]);
    });

    it('prevents duplicate email registration', function () {
        User::factory()->create(['email' => 'existing@test.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'business_name' => 'Test Biz',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'existing@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('sets trial period of 14 days', function () {
        $this->postJson('/api/v1/auth/register', [
            'business_name' => 'Trial Biz',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'trial@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $tenant = Tenant::where('name', 'Trial Biz')->first();

        expect($tenant->subscription_status)->toBe('trial');
        expect((int) now()->diffInDays($tenant->trial_ends_at, absolute: true))->toBeBetween(13, 14);
        expect($tenant->isOnTrial())->toBeTrue();
    });
});
