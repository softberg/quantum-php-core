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

use Quantum\Http\Request;
use Quantum\Http\Response;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\JWToken\JWToken;

/**
 * Class ApiAuth
 *
 * @package Quantum\Libraries\Auth
 */
class ApiAuth implements AuthenticableInterface
{
    /**
     * Common auth methods
     */
    use AuthTools;

    /**
     * @var JWToken
     */
    private $jwt;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var AuthServiceInterface
     */
    private $authService;

    /**
     * @var array
     */
    private $keys = [];

    /**
     * @var string
     */
    private $authUserKey = 'auth_user';

    /**
     * ApiAuth constructor.
     *
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
     *
     * @param string $username
     * @param $password
     * @return array|bool|mixed
     */
    public function signin($username, $password)
    {
        $user = $this->authService->get($username);
        if ($user) {
            if ($this->hasher->check($password, $user->{$this->keys['passwordKey']})) {
                $tokens = $this->getUpdatedTokens($user);

                $this->authService->update($username, [
                    $this->keys['refreshTokenKey'] => $tokens[$this->keys['refreshTokenKey']]
                ]);

                return $tokens;
            }
        }

        return false;
    }

    /**
     * Sign Out
     *
     * @return bool|mixed
     */
    public function signout()
    {
        $this->authService->update($this->user()->username, [
            $this->keys['refreshTokenKey'] => ''
        ]);

        return true;
    }

    /**
     * User
     *
     * @return array|mixed|null
     */
    public function user()
    {
        try {
            $accessToken = base64_decode(Request::getAuthorizationBearer());
            return (object)$this->jwt->retrieve($accessToken)->fetchData();
        } catch (\Exception $e) {
            if (Request::has($this->keys['refreshTokenKey'])) {
                $user = $this->checkRefreshToken();
                if ($user) {
                    return $this->user();
                }
            }
            return null;
        }
    }

    /**
     * Get Updated Tokens
     *
     * @param object $user
     * @return array
     */
    public function getUpdatedTokens($user)
    {
        return [
            $this->keys['refreshTokenKey'] => $this->generateToken(),
            $this->keys['accessTokenKey'] => base64_encode($this->jwt->setData((array)$this->filterFields($user))->compose())
        ];
    }

    /**
     * Check Refresh Token
     *
     * @return bool|mixed
     */
    private function checkRefreshToken()
    {
        $user = $this->authService->get(Request::get($this->keys['refreshTokenKey']));
        if ($user) {
            $this->setUpdatedTokens($user);
            return $user;
        }

        return false;
    }

    /**
     * Set Updated Tokens
     *
     * @param object $user
     */
    private function setUpdatedTokens($user)
    {
        $username = $user->username;

        $tokens = $this->getUpdatedTokens($user);

        $this->authService->update($username, [
            $this->keys['refreshTokenKey'] => $tokens[$this->keys['refreshTokenKey']]
        ]);

        Request::set($this->keys['refreshTokenKey'], $tokens[$this->keys['refreshTokenKey']]);
        Request::updateHeader('AUTHORIZATION', 'Bearer ' . $tokens[$this->keys['accessTokenKey']]);
        Response::set('tokens', $tokens);
    }
}
