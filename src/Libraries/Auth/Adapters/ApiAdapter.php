<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.9.5
 */

namespace Quantum\Libraries\Auth\Adapters;

use Quantum\Libraries\Auth\Contracts\AuthenticatableInterface;
use Quantum\Libraries\Auth\Contracts\AuthServiceInterface;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Libraries\Jwt\Exceptions\JwtException;
use Quantum\Libraries\Auth\Constants\AuthKeys;
use Quantum\Libraries\Auth\Traits\AuthTrait;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Jwt\JwtToken;
use Quantum\Libraries\Auth\User;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Exception;

/**
 * Class ApiAuth
 * @package Quantum\Libraries\Auth
 */
class ApiAdapter implements AuthenticatableInterface
{

    use AuthTrait;

    /**
     * @param AuthServiceInterface $authService
     * @param Mailer $mailer
     * @param Hasher $hasher
     * @param JwtToken|null $jwt
     * @throws AuthException
     */
    public function __construct(AuthServiceInterface $authService, Mailer $mailer, Hasher $hasher, JwtToken $jwt = null)
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
     * Verify OTP
     * @param int $otp
     * @param string $otpToken
     * @return array
     * @throws AuthException
     * @throws JwtException
     */
    public function verifyOtp(int $otp, string $otpToken): array
    {
        $user = $this->verifyAndUpdateOtp($otp, $otpToken);
        return $this->setUpdatedTokens($user);
    }

    /**
     * Refresh user data
     * @return bool
     * @throws JwtException
     */
    public function refreshUser(): bool
    {
        $refreshToken = Request::getHeader($this->keyFields[AuthKeys::REFRESH_TOKEN]);

        $user = $this->authService->get($this->keyFields[AuthKeys::REFRESH_TOKEN], $refreshToken);

        if($user) {
            $this->setUpdatedTokens($user);
            return true;
        }

        return false;
    }

    /**
     * Get Updated Tokens
     * @param User $user
     * @return array
     * @throws JwtException
     */
    protected function getUpdatedTokens(User $user): array
    {
        return [
            $this->keyFields[AuthKeys::REFRESH_TOKEN] => $this->generateToken(),
            $this->keyFields[AuthKeys::ACCESS_TOKEN] => base64_encode($this->jwt->setData($this->getVisibleFields($user))->compose())
        ];
    }

    /**
     * Set Updated Tokens
     * @param User $user
     * @return array
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
     * @return User|null
     */
    protected function checkRefreshToken(): ?User
    {
        return $this->authService->get(
            $this->keyFields[AuthKeys::REFRESH_TOKEN],
            Request::getHeader($this->keyFields[AuthKeys::REFRESH_TOKEN])
        );
    }

    /**
     * @return User|null
     */
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
     * @return User|null
     * @throws JwtException
     */
    private function getUserFromRefreshToken(): ?User
    {
        if (!Request::hasHeader($this->keyFields[AuthKeys::REFRESH_TOKEN])) {
            return null;
        }

        $user = $this->checkRefreshToken();

        if ($user) {
            $this->setUpdatedTokens($user);
            return $this->user();
        }

        return null;
    }
}