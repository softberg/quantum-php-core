<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Mailer\Factories;

use Quantum\Mailer\Adapters\SendinblueAdapter;
use Quantum\Mailer\Exceptions\MailerException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Mailer\Contracts\MailerInterface;
use Quantum\Mailer\Adapters\MandrillAdapter;
use Quantum\Mailer\Adapters\SendgridAdapter;
use Quantum\Mailer\Adapters\MailgunAdapter;
use Quantum\Mailer\Adapters\ResendAdapter;
use Quantum\App\Exceptions\BaseException;
use Quantum\Mailer\Adapters\SmtpAdapter;
use Quantum\Di\Exceptions\DiException;
use Quantum\Mailer\Enums\MailerType;
use Quantum\Mailer\Mailer;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * class MailerFactory
 * @package Quantum\Mailer
 */
class MailerFactory
{
    /**
     * Supported adapters
     */
    public const ADAPTERS = [
        MailerType::SMTP => SmtpAdapter::class,
        MailerType::MAILGUN => MailgunAdapter::class,
        MailerType::MANDRILL => MandrillAdapter::class,
        MailerType::SENDGRID => SendgridAdapter::class,
        MailerType::SENDINBLUE => SendinblueAdapter::class,
        MailerType::RESEND => ResendAdapter::class,
    ];

    /**
     * @var array<string, Mailer>
     */
    private static array $instances = [];

    /**
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(?string $adapter = null): Mailer
    {
        if (!config()->has('mailer')) {
            config()->import(new Setup('config', 'mailer'));
        }

        $adapter ??= config()->get('mailer.default');

        $adapterClass = self::getAdapterClass($adapter);

        if (!isset(self::$instances[$adapter])) {
            self::$instances[$adapter] = self::createInstance($adapterClass, $adapter);
        }

        return self::$instances[$adapter];
    }

    /**
     * @throws MailerException
     */
    private static function createInstance(string $adapterClass, string $adapter): Mailer
    {
        $adapterInstance = new $adapterClass(config()->get('mailer.' . $adapter));

        if (!$adapterInstance instanceof MailerInterface) {
            throw MailerException::adapterNotSupported($adapter);
        }

        return new Mailer($adapterInstance);
    }

    /**
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
