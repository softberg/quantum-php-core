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

namespace Quantum\Libraries\Auth;

use Quantum\Libraries\Mailer\MailerException;
use Quantum\Libraries\Config\ConfigException;
use Quantum\Libraries\Lang\LangException;
use Quantum\Exceptions\ServiceException;
use Quantum\Libraries\JWToken\JWToken;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Exceptions\DiException;
use Quantum\Factory\ServiceFactory;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class AuthManager
 * @package Quantum\Libraries\Auth
 */
class AuthManager
{

    const ADAPTERS = [
        'web',
        'api',
    ];

    /**
     * @var AuthenticatableInterface
     */
    private static $adapter;

    /**
     * Get Handler
     * @return AuthenticatableInterface
     * @throws AuthException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     * @throws LangException
     * @throws ServiceException
     * @throws MailerException
     */
    public static function getHandler(): AuthenticatableInterface
    {
        if (self::$adapter !== null) {
            return self::$adapter;
        }

        self::loadConfigs();

        $authAdapter = config()->get('auth.type');

        if (!in_array($authAdapter, self::ADAPTERS)) {
            throw AuthException::undefinedAuthType($authAdapter);
        }

        $authService = ServiceFactory::create(config()->get('auth.service'));

        if (!($authService instanceof AuthServiceInterface)) {
            throw AuthException::incorrectAuthService();
        }

        $mailer = mailer();
        $hasher = new Hasher();
        $jwt = null;

        if ($authAdapter == 'api') {
            $jwt = (new JWToken())
                ->setLeeway(1)
                ->setClaims((array)config()->get('auth.claims'));
        }

        $authAdapterClassName = __NAMESPACE__ . '\\Adapters\\' . ucfirst($authAdapter) . 'Adapter';

        return self::$adapter = $authAdapterClassName::getInstance($authService, $mailer, $hasher, $jwt);
    }

    /**
     * @throws AuthException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private static function loadConfigs()
    {
        if (!config()->has('auth')) {
            config()->import(new Setup('Config', 'auth'));
        }

        if (!config()->has('auth.type')) {
            throw AuthException::misconfiguredAuthConfig();
        }

        if (!config()->has('mailer') || !config()->has('mailer.current')) {
            config()->import(new Setup('config', 'mailer'));
        }
    }
}
