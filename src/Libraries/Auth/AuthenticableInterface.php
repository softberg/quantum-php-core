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
 * Interface AuthenticableInterface
 *
 * @package Quantum\Libraries\Auth
 */
interface AuthenticableInterface
{

    /**
     * AuthenticableInterface constructor.
     *
     * @param AuthServiceInterface $authService
     * @param Hasher $hasher
     * @param JWToken|null $jwt
     */
    public function __construct(AuthServiceInterface $authService, Hasher $hasher, JWToken $jwt = null);

    /**
     * Sign In
     *
     * @param Mailer $mailer
     * @param string $username
     * @param $password
     * @return mixed
     */
    public function signin($mailer, $username, $password);

    /**
     * Sign Out
     *
     * @return mixed
     */
    public function signout();

    /**
     * Check
     *
     * @return bool
     */
    public function check();

    /**
     * User
     *
     * @return mixed
     */
    public function user();
}
