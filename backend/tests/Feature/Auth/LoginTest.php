<?php

declare(strict_types=1);

use App\Domain\Auth\Models\User;

uses(\Tests\Helpers\AuthHelper::class);

describe('POST /api/v1/auth/login', function () {
    it('logs in with valid credentials', function () {
        User::factory()->create([
            'email' => 'jane@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'jane@test.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => ['id', 'first_name', 'last_name', 'email'],
                    'token',
                    'token_type',
                ],
            ]);
    });

    it('rejects invalid credentials', function () {
        User::factory()->create([
            'email' => 'jane@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'jane@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('rejects non-existent email', function () {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nobody@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    });

    it('revokes previous token on login', function () {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // First login
        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        expect($user->tokens()->count())->toBe(1);

        // Second login - should revoke first token
        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        expect($user->fresh()->tokens()->count())->toBe(1);
    });
});
