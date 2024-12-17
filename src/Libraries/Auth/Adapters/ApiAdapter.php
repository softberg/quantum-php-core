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

use Quantum\Libraries\Auth\AuthenticatableInterface;
use Quantum\Libraries\Auth\AuthServiceInterface;
use Quantum\Libraries\Mailer\MailerInterface;
use Quantum\Libraries\Auth\AuthException;
use Quantum\Libraries\JWToken\JWToken;
use Quantum\Exceptions\LangException;
use Quantum\Exceptions\JwtException;
use Quantum\Libraries\Auth\BaseAuth;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Auth\User;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Exception;

/**
 * Class ApiAuth
 * @package Quantum\Libraries\Auth
 */
class ApiAdapter extends BaseAuth implements AuthenticatableInterface
{

    /**
     * @var ApiAdapter
     */
    private static $instance;

    /**
     * @param AuthServiceInterface $authService
     * @param MailerInterface $mailer
     * @param Hasher $hasher
     * @param JWToken|null $jwt
     * @throws AuthException
     * @throws LangException
     */
    private function __construct(AuthServiceInterface $authService, MailerInterface $mailer, Hasher $hasher, JWToken $jwt = null)
    {
        $this->authService = $authService;
        $this->mailer = $mailer;
        $this->hasher = $hasher;
        $this->jwt = $jwt;

        $userSchema = $this->authService->userSchema();

        $this->verifySchema($userSchema);
    }

    /**
     * @param AuthServiceInterface $authService
     * @param MailerInterface $mailer
     * @param Hasher $hasher
     * @param JWToken|null $jwt
     * @return self
     * @throws AuthException
     * @throws LangException
     */
    public static function getInstance(AuthServiceInterface $authService, MailerInterface $mailer, Hasher $hasher, JWToken $jwt = null): self
    {
        if (self::$instance === null) {
            self::$instance = new self($authService, $mailer, $hasher, $jwt);
        }

        return self::$instance;
    }

    /**
     * Sign In
     * @param string $username
     * @param string $password
     * @return array|string
     * @throws AuthException
     * @throws JwtException
     * @throws LangException
     * @throws Exception
     */
    public function signin(string $username, string $password)
    {
        $user = $this->getUser($username, $password);

        if (filter_var(config()->get('2FA'), FILTER_VALIDATE_BOOLEAN)) {
            return $this->twoStepVerification($user);
        } else {
            return $this->setUpdatedTokens($user);
        }
    }

    /**
     * Sign Out
     * @return bool
     */
    public function signout(): bool
    {
        $refreshToken = Request::getHeader($this->keyFields[self::REFRESH_TOKEN_KEY]);

        $user = $this->authService->get($this->keyFields[self::REFRESH_TOKEN_KEY], $refreshToken);

        if ($user) {
            $this->authService->update(
                $this->keyFields[self::REFRESH_TOKEN_KEY],
                $refreshToken,
                array_merge($this->getVisibleFields($user), [$this->keyFields[self::REFRESH_TOKEN_KEY] => ''])
            );

            Request::deleteHeader($this->keyFields[self::REFRESH_TOKEN_KEY]);
            Request::deleteHeader('Authorization');
            Response::delete('tokens');

            return true;
        }

        return false;
    }

    /**
     * User
     * @return User|null
     * @throws JwtException
     */
    public function user(): ?User
    {
        try {
            $accessToken = base64_decode((string)Request::getAuthorizationBearer());
            return (new User())->setData($this->jwt->retrieve($accessToken)->fetchData());
        } catch (Exception $e) {
            if (Request::hasHeader($this->keyFields[self::REFRESH_TOKEN_KEY])) {
                $user = $this->checkRefreshToken();

                if ($user) {
                    $this->setUpdatedTokens($user);
                    return $this->user();
                }
            }

            return null;
        }
    }

    /**
     * Get Updated Tokens
     * @param User $user
     * @return array
     * @throws JwtException
     */
    public function getUpdatedTokens(User $user): array
    {
        return [
            $this->keyFields[self::REFRESH_TOKEN_KEY] => $this->generateToken(),
            $this->keyFields[self::ACCESS_TOKEN_KEY] => base64_encode($this->jwt->setData($this->getVisibleFields($user))->compose())
        ];
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
     * Check Refresh Token
     * @return User|null
     */
    protected function checkRefreshToken(): ?User
    {
        return $this->authService->get(
            $this->keyFields[self::REFRESH_TOKEN_KEY],
            Request::getHeader($this->keyFields[self::REFRESH_TOKEN_KEY])
        );
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
            $this->keyFields[self::USERNAME_KEY],
            $user->getFieldValue($this->keyFields[self::USERNAME_KEY]),
            array_merge($this->getVisibleFields($user), [$this->keyFields[self::REFRESH_TOKEN_KEY] => $tokens[$this->keyFields[self::REFRESH_TOKEN_KEY]]])
        );

        Request::setHeader($this->keyFields[self::REFRESH_TOKEN_KEY], $tokens[$this->keyFields[self::REFRESH_TOKEN_KEY]]);
        Request::setHeader('Authorization', 'Bearer ' . $tokens[$this->keyFields[self::ACCESS_TOKEN_KEY]]);
        Response::set('tokens', $tokens);

        return $tokens;
    }

}
