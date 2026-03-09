<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\RegisterTenantData;
use App\Domain\Auth\Models\User;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class RegisterTenantAction
{
    /**
     * Register a new tenant with its owner user.
     *
     * @return array{tenant: Tenant, user: User, token: string}
     */
    public function execute(RegisterTenantData $data): array
    {
        return DB::transaction(function () use ($data): array {
            $tenant = Tenant::create([
                'name' => $data->businessName,
                'slug' => $this->generateUniqueSlug($data->businessName),
                'email' => $data->email,
                'phone' => $data->phone,
                'timezone' => $data->timezone,
                'currency' => $data->currency,
                'subscription_status' => 'trial',
                'trial_ends_at' => now()->addDays(14),
            ]);

            $user = User::create([
                'first_name' => $data->firstName,
                'last_name' => $data->lastName,
                'email' => $data->email,
                'password' => $data->password,
                'phone' => $data->phone,
            ]);

            $tenant->users()->attach($user->id, [
                'id' => Str::uuid()->toString(),
                'role' => 'owner',
                'is_default' => true,
                'joined_at' => now(),
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            return [
                'tenant' => $tenant,
                'user' => $user,
                'token' => $token,
            ];
        });
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Tenant::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
