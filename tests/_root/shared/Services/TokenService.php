<?php

namespace Quantum\Tests\_root\shared\Services;

use Quantum\Libraries\Storage\Contracts\TokenServiceInterface;
use Quantum\Service\QtService;

class TokenService extends QtService implements TokenServiceInterface
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
