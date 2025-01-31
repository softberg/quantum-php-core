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

namespace Quantum\App\Factories;

use Quantum\App\Adapters\ConsoleAppAdapter;
use Quantum\App\Exceptions\AppException;
use Quantum\App\Adapters\WebAppAdapter;
use Quantum\Exceptions\BaseException;
use Quantum\App\App;

/**
 * Class AppFactory
 * @package Quantum\App
 */
class AppFactory
{

    /**
     * Supported adapters
     */
    const ADAPTERS = [
        App::WEB => WebAppAdapter::class,
        App::CONSOLE => ConsoleAppAdapter::class,
    ];

    /**
     * @var array
     */
    private static $instances = [];

    /**
     * @param string $type
     * @param string $baseDir
     * @return App
     * @throws BaseException
     */
    public static function create(string $type, string $baseDir): App
    {

        if (!isset(self::$instances[$type])) {
            self::$instances[$type] = self::createInstance($type, $baseDir);
        }

        return self::$instances[$type];
    }

    /**
     * @param string $type
     * @param string $baseDir
     * @return App
     * @throws BaseException
     */
    private static function createInstance(string $type, string $baseDir): App
    {
        if (!isset(self::ADAPTERS[$type])) {
            throw AppException::adapterNotSupported($type);
        }

        $adapterClass = self::ADAPTERS[$type];

        App::setBaseDir($baseDir);

        return new App(new $adapterClass());
    }
}