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
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\JWToken\JWToken;
use Quantum\Libraries\Mailer\Mailer;

/**
 * Class WebAuth
 * @package Quantum\Libraries\Auth
 */
class WebAuth extends BaseAuth implements AuthenticableInterface
{

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
     * WebAuth constructor.
     * @param AuthServiceInterface $authService
     * @param Hasher $hasher
     * @param JWToken|null $jwt
     */
    public function __construct(AuthServiceInterface $authService, Hasher $hasher, JWToken $jwt = null)
    {
        $this->hasher = $hasher;
        $this->authService = $authService;
        $this->keys = $this->authService->getDefinedKeys();
    }

    /**
     * Sign In
     * @param Mailer $mailer
     * @param string $username
     * @param string $password
     * @param boolean $remember
     * @return mixed|boolean
     * @throws AuthException
     */
    public function signin($mailer, $username, $password, $remember = false)
    {
        $user = $this->authService->get($this->keys['usernameKey'], $username);

        if (empty($user)) {
            throw new AuthException(ExceptionMessages::INCORRECT_AUTH_CREDENTIALS);
        }

        if (!$this->hasher->check($password, $user[$this->keys['passwordKey']])) {
            throw new AuthException(ExceptionMessages::INCORRECT_AUTH_CREDENTIALS);
        }

        if (!$this->isActivated($user)) {
            throw new AuthException(ExceptionMessages::INACTIVE_ACCOUNT);
        }

        if ($remember) {
            $this->setRememberToken($user);
        }

        if (filter_var(config()->get('2SV'), FILTER_VALIDATE_BOOLEAN)) {

            $otp_token = $this->generateOtpToken($user[$this->keys['usernameKey']]);

            $time = new \DateTime();

            $time->add(new \DateInterval('PT' . config()->get('otp_expiry_time') . 'M'));

            $otp_expiry_time = $time->format('Y-m-d H:i');

            $this->towStepVerification($mailer, $user, $otp_expiry_time, $otp_token);

            return $otp_token;

        } else {

            session()->set($this->authUserKey, $this->filterFields($user));
        }

        return true;
    }

    /**
     * Sign Out
     * @throws \Exception
     */
    public function signout()
    {
        if (session()->has($this->authUserKey)) {
            session()->delete($this->authUserKey);
            $this->removeRememberToken();
        }
    }

    /**
     * User
     * @return mixed|null
     * @throws \Exception
     */
    public function user()
    {
        if (session()->has($this->authUserKey)) {
            return (object) session()->get($this->authUserKey);
        } else if (cookie()->has($this->keys['rememberTokenKey'])) {
            $user = $this->checkRememberToken();
            if ($user) {
                return $this->user();
            }
        }
        return null;
    }

    /**
     * Verify
     * @param int $code
     * @return bool
     * @throws \Exception
     */

    public function verify($code, $otp_token)
    {
        $user = $this->authService->get($this->keys['otpToken'], $otp_token);

        if (new \DateTime() >= new \DateTime($user[$this->keys['otpExpiryIn']])){
            throw new AuthException(ExceptionMessages::VERIFICATION_CODE_EXPIRY_IN);
        }

        if ($code != $user[$this->keys['otpKey']]) {
            throw new AuthException(ExceptionMessages::INCORRECT_VERIFICATION_CODE);
        }

        $this->authService->update($this->keys['usernameKey'], $user[$this->keys['usernameKey']], [
            $this->keys['otpKey'] => null,
            $this->keys['otpExpiryIn'] => null,
            $this->keys['otpToken'] => null,
        ]);

        $user = $this->authService->get($this->keys['usernameKey'], $user[$this->keys['usernameKey']]);

        session()->set($this->authUserKey, $this->filterFields($user));

        return true;
    }

    /**
     * Resend Otp
     * @param Mailer $mailer
     * @param string $otp_token
     * @return bool|mixed
     * @throws \Exception
     */

    public function resendOtp($mailer, $otp_token)
    {
        $user = $this->authService->get($this->keys['otpToken'], $otp_token);

        if (empty($user)) {

            return false;
        }

        $otp_token = $this->generateOtpToken($user[$this->keys['usernameKey']]);

        $time = new \DateTime();

        $time->add(new \DateInterval('PT' . config()->get('otp_expiry_time') . 'M'));

        $stamp = $time->format('Y-m-d H:i');

        $this->towStepVerification($mailer, $user, $stamp, $otp_token);

        return $otp_token;
    }

    /**
     * Check Remember Token
     * @return bool|mixed
     * @throws \Exception
     */
    private function checkRememberToken()
    {
        $user = $this->authService->get($this->keys['rememberTokenKey'], cookie()->get($this->keys['rememberTokenKey']));
        if (!empty($user)) {
            $this->setRememberToken($user);
            return $user;
        }
        return false;
    }

    /**
     * Set Remember Token
     * @param array $user
     * @throws \Exception
     */
    private function setRememberToken(array $user)
    {
        $rememberToken = $this->generateToken();

        $this->authService->update($this->keys['usernameKey'], $user[$this->keys['usernameKey']], [
            $this->keys['rememberTokenKey'] => $rememberToken
        ]);

        session()->set($this->authUserKey, $this->filterFields($user));
        cookie()->set($this->keys['rememberTokenKey'], $rememberToken);
    }

    /**
     * Remove Remember Token
     * @throws \Exception
     */
    private function removeRememberToken()
    {
        if (cookie()->has($this->keys['rememberTokenKey'])) {
            $user = $this->authService->get($this->keys['rememberTokenKey'], cookie()->get($this->keys['rememberTokenKey']));

            if (!empty($user)) {
                $this->authService->update($this->keys['rememberTokenKey'], $user[$this->keys['rememberTokenKey']], [
                    $this->keys['rememberTokenKey'] => ''
                ]);
            }

            cookie()->delete($this->keys['rememberTokenKey']);
        }
    }
}
