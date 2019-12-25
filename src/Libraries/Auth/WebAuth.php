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
 * @since 1.9.0
 */

namespace Quantum\Libraries\Auth;

use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\JWToken\JWToken;

/**
 * Class WebAuth
 *
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
     *
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
     *
     * @param string $username
     * @param string $password
     * @param bool $remember
     * @return bool
     * @throws \Exception
     */
    public function signin($username, $password, $remember = false)
    {
        $user = $this->authService->get($this->keys['usernameKey'], $username);
        if ($user) {
            if ($this->hasher->check($password, $user->{$this->keys['passwordKey']})) {
                if ($remember) {
                    $this->setRememberToken($user);
                }

                session()->set($this->authUserKey, $this->filterFields($user));

                return true;
            }
        }

        return false;
    }

    /**
     * Sign Out
     *
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
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function user()
    {
        if (session()->has($this->authUserKey)) {
            return (object)session()->get($this->authUserKey);
        } else if (cookie()->has($this->keys['rememberTokenKey'])) {
            $user = $this->checkRememberToken();
            if ($user) {
                return $this->user();
            }
        }
        return null;
    }

    /**
     * Check Remember Token
     *
     * @return bool|mixed
     * @throws \Exception
     */
    private function checkRememberToken()
    {
        $user = $this->authService->get($this->keys['rememberTokenKey'], cookie()->get($this->keys['rememberTokenKey']));
        if ($user) {
            $this->setRememberToken($user);
            return $user;
        }

        return false;
    }

    /**
     * Set Remember Token
     *
     * @param object $user
     * @throws \Exception
     */
    private function setRememberToken($user)
    {
        $rememberToken = $this->generateToken();

        $this->authService->update($user->username, [
            $this->keys['rememberTokenKey'] => $rememberToken
        ]);

        session()->set($this->authUserKey, $this->filterFields($user));
        cookie()->set($this->keys['rememberTokenKey'], $rememberToken);
    }

    /**
     * Remove Remember Token
     *
     * @throws \Exception
     */
    private function removeRememberToken()
    {
        if (cookie()->has($this->keys['rememberTokenKey'])) {
            $user = $this->authService->get($this->keys['rememberTokenKey'], cookie()->get($this->keys['rememberTokenKey']));

            $this->authService->update($user->{$this->keys['rememberTokenKey']}, [
                $this->keys['rememberTokenKey'] => ''
            ]);

            cookie()->delete($this->keys['rememberTokenKey']);
        }
    }

}
