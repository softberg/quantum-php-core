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
 * @since 2.6.0
 */

namespace Quantum\Libraries\Auth;

use Quantum\Libraries\JWToken\JWToken;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class ApiAuth
 * @package Quantum\Libraries\Auth
 */
class ApiAuth extends BaseAuth implements AuthenticableInterface
{

    /**
     * Instance of ApiAuth
     * @var \Quantum\Libraries\Auth\ApiAuth
     */
    private static $instance;

    /**
     * ApiAuth constructor.
     * @param \Quantum\Libraries\Auth\AuthServiceInterface $authService
     * @param \Quantum\Libraries\Mailer\Mailer $mailer
     * @param \Quantum\Libraries\Hasher\Hasher $hasher
     * @param \Quantum\Libraries\JWToken\JWToken|null $jwt
     * @throws \Quantum\Exceptions\AuthException
     */
    private function __construct(AuthServiceInterface $authService, Mailer $mailer, Hasher $hasher, JWToken $jwt = null)
    {
        $this->mailer = $mailer;
        $this->jwt = $jwt;
        $this->hasher = $hasher;
        $this->authService = $authService;

        $userSchema = $this->authService->userSchema();

        $this->verifySchema($userSchema);
    }

    /**
     * Get Instance
     * @param \Quantum\Libraries\Auth\AuthServiceInterface $authService
     * @param \Quantum\Libraries\Mailer\Mailer $mailer
     * @param \Quantum\Libraries\Hasher\Hasher $hasher
     * @param \Quantum\Libraries\JWToken\JWToken|null $jwt
     * @return \Quantum\Libraries\Auth\ApiAuth
     * @throws \Quantum\Exceptions\AuthException
     */
    public static function getInstance(AuthServiceInterface $authService, Mailer $mailer, Hasher $hasher, JWToken $jwt = null): ApiAuth
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
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \Quantum\Exceptions\AuthException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\JwtException
     * @throws \ReflectionException
     */
    public function signin(string $username, string $password)
    {
        $user = $this->getUser($username, $password);

        if (filter_var(config()->get('2SV'), FILTER_VALIDATE_BOOLEAN)) {
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
                [
                    $this->authUserKey => $this->getVisibleFields($user),
                    $this->keyFields[self::REFRESH_TOKEN_KEY] => ''
                ]
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
     * @return \Quantum\Libraries\Auth\User|null
     * @throws \Quantum\Exceptions\JwtException
     */
    public function user(): ?User
    {
        try {
            $accessToken = base64_decode((string)Request::getAuthorizationBearer());
            return (new User())->setData($this->jwt->retrieve($accessToken)->fetchData());
        } catch (\Exception $e) {
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
     * @param \Quantum\Libraries\Auth\User $user
     * @return array
     * @throws \Quantum\Exceptions\JwtException
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
     * @throws \Quantum\Exceptions\AuthException
     */
    public function verifyOtp(int $otp, string $otpToken): array
    {
        $user = $this->verifyAndUpdateOtp($otp, $otpToken);
        return $this->setUpdatedTokens($user);
    }

    /**
     * Check Refresh Token
     * @return \Quantum\Libraries\Auth\User|null
     */
    protected function checkRefreshToken(): ?User
    {
        return $this->authService->get($this->keyFields[self::REFRESH_TOKEN_KEY], Request::getHeader($this->keyFields[self::REFRESH_TOKEN_KEY]));
    }

    /**
     * Set Updated Tokens
     * @param \Quantum\Libraries\Auth\User $user
     * @return array
     * @throws \Quantum\Exceptions\JwtException
     */
    protected function setUpdatedTokens(User $user): array
    {
        $tokens = $this->getUpdatedTokens($user);

        $this->authService->update(
            $this->keyFields[self::USERNAME_KEY],
            $user->getFieldValue($this->keyFields[self::USERNAME_KEY]),
            [
                $this->authUserKey => $this->getVisibleFields($user),
                $this->keyFields[self::REFRESH_TOKEN_KEY] => $tokens[$this->keyFields[self::REFRESH_TOKEN_KEY]]
            ]
        );

        Request::setHeader($this->keyFields[self::REFRESH_TOKEN_KEY], $tokens[$this->keyFields[self::REFRESH_TOKEN_KEY]]);
        Request::setHeader('Authorization', 'Bearer ' . $tokens[$this->keyFields[self::ACCESS_TOKEN_KEY]]);
        Response::set('tokens', $tokens);

        return $tokens;
    }

}
