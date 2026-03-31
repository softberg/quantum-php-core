<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Auth\Adapters;

use Quantum\Auth\Contracts\AuthenticatableInterface;
use Quantum\Auth\Contracts\AuthServiceInterface;
use Quantum\Auth\Exceptions\AuthException;
use Quantum\Jwt\Exceptions\JwtException;
use Quantum\Auth\Traits\AuthTrait;
use Quantum\Auth\Enums\AuthKeys;
use Quantum\Mailer\Mailer;
use Quantum\Hasher\Hasher;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Quantum\Jwt\JwtToken;
use Quantum\Auth\User;
use Exception;

/**
 * Class ApiAuth
 * @package Quantum\Auth
 */
class JwtAuthAdapter implements AuthenticatableInterface
{
    use AuthTrait;

    protected JwtToken $jwt;

    /**
     * @throws AuthException
     */
    public function __construct(AuthServiceInterface $authService, Mailer $mailer, Hasher $hasher, JwtToken $jwt)
    {
        $this->authService = $authService;
        $this->mailer = $mailer;
        $this->hasher = $hasher;
        $this->jwt = $jwt;

        $this->verifySchema($this->authService->userSchema());
    }

    /**
     * @inheritDoc
     * @throws AuthException
     * @throws JwtException
     * @throws Exception
     */
    public function signin(string $username, string $password)
    {
        $user = $this->getUser($username, $password);

        if ($this->isTwoFactorEnabled()) {
            return $this->twoStepVerification($user);
        }

        return $this->setUpdatedTokens($user);
    }

    /**
     * @inheritDoc
     */
    public function signout(): bool
    {
        $refreshToken = Request::getHeader($this->keyFields[AuthKeys::REFRESH_TOKEN]);

        $user = $this->authService->get($this->keyFields[AuthKeys::REFRESH_TOKEN], $refreshToken);

        if ($user) {
            $this->authService->update(
                $this->keyFields[AuthKeys::REFRESH_TOKEN],
                $refreshToken,
                array_merge($this->getVisibleFields($user), [$this->keyFields[AuthKeys::REFRESH_TOKEN] => ''])
            );

            Request::deleteHeader($this->keyFields[AuthKeys::REFRESH_TOKEN]);
            Request::deleteHeader('Authorization');
            Response::delete('tokens');

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     * @throws JwtException
     */
    public function user(): ?User
    {
        try {
            return $this->getUserFromAccessToken();
        } catch (Exception $e) {
            return $this->getUserFromRefreshToken();
        }
    }

    /**
     * Refresh user data
     * @throws JwtException
     */
    public function refreshUser(string $uuid): bool
    {
        $user = $this->authService->get('uuid', $uuid);

        if (!$user) {
            return false;
        }

        $this->setUpdatedTokens($user);

        return true;
    }

    /**
     * Verify OTP
     * @throws AuthException
     * @throws JwtException
     * @return array<string, string>
     */
    public function verifyOtp(int $otp, string $otpToken): array
    {
        $user = $this->verifyAndUpdateOtp($otp, $otpToken);
        return $this->setUpdatedTokens($user);
    }

    /**
     * Get Updated Tokens
     * @return array<string, string>
     * @throws JwtException
     */
    protected function getUpdatedTokens(User $user): array
    {

        return [
            $this->keyFields[AuthKeys::REFRESH_TOKEN] => $this->generateToken(),
            $this->keyFields[AuthKeys::ACCESS_TOKEN] => base64_encode($this->jwt->setData($this->getVisibleFields($user))->compose()),
        ];
    }

    /**
     * Set Updated Tokens
     * @return array<string, string>
     * @throws JwtException
     */
    protected function setUpdatedTokens(User $user): array
    {
        $tokens = $this->getUpdatedTokens($user);

        $this->authService->update(
            $this->keyFields[AuthKeys::USERNAME],
            $user->getFieldValue($this->keyFields[AuthKeys::USERNAME]),
            array_merge($this->getVisibleFields($user), [$this->keyFields[AuthKeys::REFRESH_TOKEN] => $tokens[$this->keyFields[AuthKeys::REFRESH_TOKEN]]])
        );

        Request::setHeader($this->keyFields[AuthKeys::REFRESH_TOKEN], $tokens[$this->keyFields[AuthKeys::REFRESH_TOKEN]]);
        Request::setHeader('Authorization', 'Bearer ' . $tokens[$this->keyFields[AuthKeys::ACCESS_TOKEN]]);
        Response::set('tokens', $tokens);

        return $tokens;
    }

    /**
     * Check Refresh Token
     */
    protected function checkRefreshToken(): ?User
    {
        return $this->authService->get(
            $this->keyFields[AuthKeys::REFRESH_TOKEN],
            Request::getHeader($this->keyFields[AuthKeys::REFRESH_TOKEN])
        );
    }

    private function getUserFromAccessToken(): ?User
    {
        $authorizationBearer = Request::getAuthorizationBearer();

        if (!$authorizationBearer) {
            return null;
        }

        $accessToken = base64_decode($authorizationBearer);

        $userData = $this->jwt->retrieve($accessToken)->fetchData();

        return $userData ? (new User())->setData($userData) : null;
    }

    /**
     * @throws JwtException
     */
    private function getUserFromRefreshToken(): ?User
    {
        if (!Request::hasHeader($this->keyFields[AuthKeys::REFRESH_TOKEN])) {
            return null;
        }

        $user = $this->checkRefreshToken();

        if ($user instanceof User) {
            $this->setUpdatedTokens($user);
            return $this->user();
        }

        return null;
    }
}
