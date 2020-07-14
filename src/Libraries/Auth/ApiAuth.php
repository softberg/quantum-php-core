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

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\AuthException;
use Quantum\Libraries\JWToken\JWToken;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class ApiAuth
 *
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
     * @param string $password
     * @return array
     * @throws AuthException
     */
    public function signin($username, $password)
    {
        $user = $this->authService->get($this->keys['usernameKey'], $username);

        if (!$user) {
            throw new AuthException(ExceptionMessages::INCORRECT_AUTH_CREDENTIALS);
        }

        if (!$this->hasher->check($password, $user[$this->keys['passwordKey']])) {
            throw new AuthException(ExceptionMessages::INCORRECT_AUTH_CREDENTIALS);
        }
        if (!$this->isActivated($user)) {
            throw new AuthException(ExceptionMessages::INACTIVE_ACCOUNT);
        }

        $tokens = $this->setUpdatedTokens($user);

        return $tokens;
    }

    /**
     * Sign Out
     *
     * @return bool|mixed
     */
    public function signout()
    {

        $refreshToken = Request::getHeader($this->keys['refreshTokenKey']);

        $user = $this->authService->get($this->keys['refreshTokenKey'], $refreshToken);

        if ($user) {
            $this->authService->update(
                    $this->keys['refreshTokenKey'],
                    $refreshToken,
                    [
                        $this->authUserKey => $user,
                        $this->keys['refreshTokenKey'] => ''
                    ]
            );

            Request::deleteHeader($this->keys['refreshTokenKey']);
            Request::deleteHeader('Authorization');
            Response::delete('tokens');

            return true;
        }

        return false;
    }

    /**
     * User
     *
     * @return mixed|object|null
     */
    public function user()
    {
        try {
            $accessToken = base64_decode(Request::getAuthorizationBearer());
            return (object) $this->jwt->retrieve($accessToken)->fetchData();
        } catch (\Exception $e) {
            if (Request::hasHeader($this->keys['refreshTokenKey'])) {
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
    public function getUpdatedTokens(array $user)
    {
        return [
            $this->keys['refreshTokenKey'] => $this->generateToken(),
            $this->keys['accessTokenKey'] => base64_encode($this->jwt->setData($this->filterFields($user))->compose())
        ];
    }

    /**
     * Check Refresh Token
     *
     * @return bool|mixed
     */
    protected function checkRefreshToken()
    {
        $user = $this->authService->get($this->keys['refreshTokenKey'], Request::getHeader($this->keys['refreshTokenKey']));

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
    protected function setUpdatedTokens($user)
    {
        $tokens = $this->getUpdatedTokens($user);

        $this->authService->update(
                $this->keys['usernameKey'],
                $user[$this->keys['usernameKey']],
                [
                    $this->authUserKey => $user,
                    $this->keys['refreshTokenKey'] => $tokens[$this->keys['refreshTokenKey']]
                ]
        );

        Request::setHeader($this->keys['refreshTokenKey'], $tokens[$this->keys['refreshTokenKey']]);
        Request::setHeader('Authorization', 'Bearer ' . $tokens[$this->keys['accessTokenKey']]);
        Response::set('tokens', $tokens);

        return $tokens;
    }

}
