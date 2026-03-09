<?php

declare(strict_types=1);

namespace App\Domain\Auth\DTOs;

final readonly class RegisterTenantData
{
    public function __construct(
        public string $businessName,
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
        public ?string $phone = null,
        public string $timezone = 'UTC',
        public string $currency = 'USD',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            businessName: $data['business_name'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            password: $data['password'],
            phone: $data['phone'] ?? null,
            timezone: $data['timezone'] ?? 'UTC',
            currency: $data['currency'] ?? 'USD',
        );
    }
}
