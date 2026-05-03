<?php

namespace App\DTOs\Auth;

use App\Enums\UserRole;
use Illuminate\Http\Request;

readonly class authenticateDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}


    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();

        return new self(
            email: $data['email'],
            password: $data['password'],
        );
    }


    public function toArray(): array
    {
        return [
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }
}
