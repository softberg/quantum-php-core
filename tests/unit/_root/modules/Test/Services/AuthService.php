<?php

namespace Quantum\Tests\_root\modules\test\Services;
use Quantum\Libraries\Auth\AuthServiceInterface;
use Quantum\Libraries\Auth\User;
use Quantum\Mvc\QtService;

class AuthService extends QtService implements AuthServiceInterface
{

    public function get(string $field, ?string $value): ?User
    {
        return new User();
    }

    public function add(array $data): User
    {
        return new User();
    }

    public function update(string $field, ?string $value, array $data): ?User
    {
        return new User();
    }

    public function userSchema(): array
    {
        return  [
            'id' => ['name' => 'id', 'visible' => false],
            'firstname' => ['name' => 'firstname', 'visible' => true],
            'lastname' => ['name' => 'lastname', 'visible' => true],
            'role' => ['name' => 'role', 'visible' => true],
            'username' => ['name' => 'email', 'visible' => true],
            'password' => ['name' => 'password', 'visible' => false],
            'activationToken' => ['name' => 'activation_token', 'visible' => false],
            'rememberToken' => ['name' => 'remember_token', 'visible' => false],
            'resetToken' => ['name' => 'reset_token', 'visible' => false],
            'accessToken' => ['name' => 'access_token', 'visible' => false],
            'refreshToken' => ['name' => 'refresh_token', 'visible' => false],
            'otp' => ['name' => 'otp', 'visible' => false],
            'otpExpiry' => ['name' => 'otp_expires', 'visible' => false],
            'otpToken' => ['name' => 'otp_token', 'visible' => false],
        ];
    }
}
