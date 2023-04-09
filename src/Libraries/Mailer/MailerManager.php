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
 * @since 2.9.0
 */

namespace Quantum\Libraries\Mailer;

use Quantum\Exceptions\ConfigException;
use Quantum\Exceptions\MailerException;
use Quantum\Exceptions\LangException;
use Quantum\Exceptions\AppException;
use Quantum\Exceptions\DiException;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * class MailerManager
 * @package Quantum\Libraries\Mailer
 */
class MailerManager
{

    /**
     * Available mail adapters
     */
    const ADAPTERS = [
        'smtp',
        'sendinblue',
        'sendgrid',
        'mandrill',
        'mailgun',
    ];

    /**
     * @var MailerInterface
     */
    private static $adapter;

    /**
     * Get Handler
     * @return MailerInterface
     * @throws ConfigException
     * @throws DiException
     * @throws MailerException
     * @throws ReflectionException
     * @throws LangException
     */
    public static function getHandler(): MailerInterface
    {
        if (self::$adapter !== null) {
            return self::$adapter;
        }

        if (!config()->has('mailer')) {
            config()->import(new Setup('config', 'mailer'));
        }

        $mailerAdapter = config()->get('mailer.current');

        if (!in_array($mailerAdapter, self::ADAPTERS)) {
            throw MailerException::unsupportedAdapter($mailerAdapter);
        }

        $currentMailer = config()->get('mailer.current');

        $mailerAdapterClassName = __NAMESPACE__ . '\\Adapters\\' . ucfirst($currentMailer) . 'Adapter';

        return self::$adapter = $mailerAdapterClassName::getInstance(config()->get('mailer.' . $currentMailer));
    }

    /**
     * @param string $method
     * @param array|null $arguments
     * @return mixed
     * @throws AppException
     */
    public function __call(string $method, ?array $arguments)
    {
        if (!method_exists(self::$adapter, $method)) {
            throw AppException::methodNotSupported($method, get_class(self::$adapter));
        }

        return self::$adapter->$method(...$arguments);
    }

}
