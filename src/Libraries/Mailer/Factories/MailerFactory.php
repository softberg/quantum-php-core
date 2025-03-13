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
 * @since 2.9.6
 */

namespace Quantum\Libraries\Mailer\Factories;

use Quantum\Libraries\Mailer\Exceptions\MailerException;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Mailer\Adapters\SendinblueAdapter;
use Quantum\Libraries\Mailer\Adapters\SendgridAdapter;
use Quantum\Libraries\Mailer\Adapters\MandrillAdapter;
use Quantum\Libraries\Mailer\Adapters\MailgunAdapter;
use Quantum\Libraries\Mailer\Adapters\SmtpAdapter;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * class MailerFactory
 * @package Quantum\Libraries\Mailer
 */
class MailerFactory
{

    /**
     * Supported adapters
     */
    const ADAPTERS = [
        Mailer::SMTP => SmtpAdapter::class,
        Mailer::MAILGUN => MailgunAdapter::class,
        Mailer::MANDRILL => MandrillAdapter::class,
        Mailer::SENDGRID => SendgridAdapter::class,
        Mailer::SENDINBLUE => SendinblueAdapter::class,
    ];

    /**
     * @var Mailer|null
     */
    private static $instance = null;

    /**
     * @return Mailer
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(): Mailer
    {
        if (self::$instance === null) {
            return self::$instance = self::createInstance();
        }

        return self::$instance;
    }

    /**
     * @return Mailer
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private static function createInstance(): Mailer
    {
        if (!config()->has('mailer')) {
            config()->import(new Setup('config', 'mailer'));
        }

        $adapter = config()->get('mailer.default');

        $adapterClass = self::getAdapterClass($adapter);

        return new Mailer(new $adapterClass(config()->get('mailer.' . $adapter)));
    }

    /**
     * @param string $adapter
     * @return string
     * @throws BaseException
     */
    private static function getAdapterClass(string $adapter): string
    {
        if (!array_key_exists($adapter, self::ADAPTERS)) {
            throw MailerException::adapterNotSupported($adapter);
        }

        return self::ADAPTERS[$adapter];
    }
}