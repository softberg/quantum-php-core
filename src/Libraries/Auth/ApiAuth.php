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
 * @since 2.0.0
 */

namespace Quantum\Libraries\Auth;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\AuthException;
use Quantum\Libraries\JWToken\JWToken;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Quantum\Libraries\Mailer\Mailer;

/**
 * Class ApiAuth
 * @package Quantum\Libraries\Auth
 */
class ApiAuth extends BaseAuth implements AuthenticableInterface
{

    /**
     * @var JWToken
     */
    protected $jwt;

    /**
     * @var Hasher
     */
    protected $hasher;

    /**
     * @var AuthServiceInterface
     */
    protected $authService;

    /**
     * @var array
     */
    protected $keys = [];

    /**
     * @var string
     */
    protected $authUserKey = 'auth_user';

    /**
     * ApiAuth constructor.
     * @param AuthServiceInterface $authService
     * @param Hasher $hasher
     * @param JWToken|null $jwt
     */
    public function __construct(AuthServiceInterface $authService, Hasher $hasher, JWToken $jwt = null)
    {
        $this->jwt = $jwt;
        $this->hasher = $hasher;
        $this->authService = $authService;
        $this->keys = $this->authService->getDefinedKeys();
    }

    /**
     * Sign In
     * @param string $username
     * @param string $password
     * @return string|array
     * @throws AuthException
     */
    public function signin($mailer, $username, $password)
    {
        $user = $this->authService->get($this->keys[self::USERNAME_KEY], $username);

        if (empty($user)) {
            throw new AuthException(ExceptionMessages::INCORRECT_AUTH_CREDENTIALS);
        }

        if (!$this->hasher->check($password, $user[$this->keys[self::PASSWORD_KEY]])) {
            throw new AuthException(ExceptionMessages::INCORRECT_AUTH_CREDENTIALS);
        }
        
        if (!$this->isActivated($user)) {
            throw new AuthException(ExceptionMessages::INACTIVE_ACCOUNT);
        }

        if (filter_var(config()->get('2SV'), FILTER_VALIDATE_BOOLEAN)) {
            $otpToken = $this->twoStepVerification($mailer, $user);
            return $otpToken;

        } else {
            $tokens = $this->setUpdatedTokens($user);
            return $tokens;
        }
    }

    /**
     * Sign Out
     * @return bool|mixed
     */
    public function signout()
    {
        $refreshToken = Request::getHeader($this->keys[self::REFRESH_TOKEN_KEY]);

        $user = $this->authService->get($this->keys[self::REFRESH_TOKEN_KEY], $refreshToken);

        if (!empty($user)) {
            $this->authService->update(
                    $this->keys[self::REFRESH_TOKEN_KEY],
                    $refreshToken,
                    [
                        $this->authUserKey => $user,
                        $this->keys[self::REFRESH_TOKEN_KEY] => ''
                    ]
            );

            Request::deleteHeader($this->keys[self::REFRESH_TOKEN_KEY]);
            Request::deleteHeader('Authorization');
            Response::delete('tokens');

            return true;
        }

        return false;
    }

    /**
     * User
     * @return object|null
     */
    public function user()
    {
        try {
            $accessToken = base64_decode(Request::getAuthorizationBearer());
            return (object) $this->jwt->retrieve($accessToken)->fetchData();
        } catch (\Exception $e) {
            if (Request::hasHeader($this->keys[self::REFRESH_TOKEN_KEY])) {
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
     * @param array $user
     * @return array
     */
    public function getUpdatedTokens(array $user)
    {
        return [
            $this->keys[self::REFRESH_TOKEN_KEY] => $this->generateToken(),
            $this->keys[self::ACCESS_TOKEN_KEY] => base64_encode($this->jwt->setData($this->filterFields($user))->compose())
        ];
    }

    /**
     * Verify OTP
     * @param integer $otp
     * @param string $otpToken
     * @return array
     * @throws AuthException
     */
    public function verify($otp, $otpToken)
    {
        $user = $this->authService->get($this->keys[self::OTP_TOKEN_KEY], $otpToken);

        if (empty($user) || $otp != $user[$this->keys[self::OTP_KEY]]) {
            throw new AuthException(ExceptionMessages::INCORRECT_VERIFICATION_CODE);
        }
        
        if (new \DateTime() >= new \DateTime($user[$this->keys[self::OTP_EXPIRY_KEY]])){
            throw new AuthException(ExceptionMessages::VERIFICATION_CODE_EXPIRED);
        }

        $this->authService->update(
                $this->keys[self::USERNAME_KEY], 
                $user[$this->keys[self::USERNAME_KEY]], 
                [
                    $this->keys[self::OTP_KEY] => null,
                    $this->keys[self::OTP_EXPIRY_KEY] => null,
                    $this->keys[self::OTP_TOKEN_KEY] => null,
                ]
        );

        $tokens = $this->setUpdatedTokens($this->filterFields($user));

        return $tokens;
    }

    /**
     * Resend OTP
     * @param Mailer $mailer
     * @param string $otpToken
     * @return string
     * @throws AuthException
     */

    public function resendOtp(Mailer $mailer, $otpToken)
    {
        $user = $this->authService->get($this->keys[self::OTP_TOKEN_KEY], $otpToken);

        if (empty($user)) {
            throw new AuthException(ExceptionMessages::INCORRECT_AUTH_CREDENTIALS);
        }

        return $this->twoStepVerification($mailer, $user);
    }

    /**
     * Check Refresh Token
     * @return bool|mixed
     */
    protected function checkRefreshToken()
    {
        $user = $this->authService->get($this->keys[self::REFRESH_TOKEN_KEY], Request::getHeader($this->keys[self::REFRESH_TOKEN_KEY]));

        if (!empty($user)) {
            return $user;
        }

        return false;
    }

    /**
     * Set Updated Tokens
     * @param array $user
     * @return array
     */
    protected function setUpdatedTokens(array $user)
    {
        $tokens = $this->getUpdatedTokens($user);

        $this->authService->update(
                $this->keys[self::USERNAME_KEY],
                $user[$this->keys[self::USERNAME_KEY]],
                [
                    $this->authUserKey => $user,
                    $this->keys[self::REFRESH_TOKEN_KEY] => $tokens[$this->keys[self::REFRESH_TOKEN_KEY]]
                ]
        );

        Request::setHeader($this->keys[self::REFRESH_TOKEN_KEY], $tokens[$this->keys[self::REFRESH_TOKEN_KEY]]);
        Request::setHeader('Authorization', 'Bearer ' . $tokens[$this->keys[self::ACCESS_TOKEN_KEY]]);
        Response::set('tokens', $tokens);

        return $tokens;
    }

}
