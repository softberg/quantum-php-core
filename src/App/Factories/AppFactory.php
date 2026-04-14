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

namespace Quantum\App\Factories;

use Quantum\App\Adapters\ConsoleAppAdapter;
use Quantum\App\Exceptions\BaseException;
use Quantum\App\Exceptions\AppException;
use Quantum\App\Adapters\WebAppAdapter;
use Quantum\App\Enums\AppType;
use Quantum\App\AppContext;
use Quantum\Di\DiContainer;
use Quantum\App\App;
use Quantum\Di\Di;

/**
 * Class AppFactory
 * @package Quantum\App
 */
class AppFactory
{
    /**
     * Supported adapters
     */
    public const ADAPTERS = [
        AppType::WEB => WebAppAdapter::class,
        AppType::CONSOLE => ConsoleAppAdapter::class,
    ];

    /**
     * @var array<string, App>
     */
    private static array $instances = [];

    /**
     * @throws BaseException
     */
    public static function create(string $type, string $baseDir): App
    {

        if (!isset(self::$instances[$type])) {
            self::$instances[$type] = self::createInstance($type, $baseDir);
        }

        return self::$instances[$type];
    }

    public static function destroy(string $type): void
    {
        unset(self::$instances[$type]);
    }

    /**
     * @throws BaseException
     */
    private static function createInstance(string $type, string $baseDir): App
    {
        if (!isset(self::ADAPTERS[$type])) {
            throw AppException::adapterNotSupported($type);
        }

        $container = new DiContainer();
        Di::setCurrent($container);

        $context = new AppContext($type, $baseDir, $container);
        App::setContext($context);

        $adapterClass = self::ADAPTERS[$type];

        return new App(new $adapterClass($context));
    }
}
