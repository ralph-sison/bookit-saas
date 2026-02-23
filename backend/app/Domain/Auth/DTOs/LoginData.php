<?php

declare(strict_types=1);

namespace App\Domain\Auth\DTOs;

final readonly class LoginData
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
        );
    }
}
