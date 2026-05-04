<?php

namespace Quantum\Tests\_root\shared\Services;

use Quantum\Storage\Contracts\TokenServiceInterface;
use Quantum\Service\Service;

class TokenService extends Service implements TokenServiceInterface
{
    public function getAccessToken(): string
    {
        return 'xxxyyyzz';
    }

    public function getRefreshToken(): string
    {
        return 'aaabbbccc';
    }

    public function saveTokens(string $accessToken, ?string $refreshToken = null): bool
    {
        return true;
    }
}
