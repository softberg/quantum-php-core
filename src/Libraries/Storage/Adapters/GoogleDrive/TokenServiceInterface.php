<?php

namespace Quantum\Libraries\Storage\Adapters\GoogleDrive;

interface TokenServiceInterface
{

    public function getAccessToken(): string;

    public function getRefreshToken(): string;

    public function saveTokens(string $accessToken, ?string $refreshToken = null): bool;

}