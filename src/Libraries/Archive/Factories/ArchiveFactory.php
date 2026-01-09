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
 * @since 3.0.0
 */

namespace Quantum\Libraries\Archive\Factories;

use Quantum\Libraries\Archive\Exceptions\ArchiveException;
use Quantum\Libraries\Archive\Adapters\PharAdapter;
use Quantum\Libraries\Archive\Adapters\ZipAdapter;
use Quantum\App\Exceptions\BaseException;
use Quantum\Libraries\Archive\Archive;

/**
 * Class Cryptor
 * @package Quantum\Libraries\Encryption
 */
class ArchiveFactory
{
    /**
     * Supported adapters
     */
    public const ADAPTERS = [
        Archive::PHAR => PharAdapter::class,
        Archive::ZIP => ZipAdapter::class,
    ];

    /**
     * @var array
     */
    private static $instances = [];

    /**
     * @param string $type
     * @return Archive
     * @throws BaseException
     */
    public static function get(string $type = Archive::PHAR): Archive
    {
        if (!isset(self::$instances[$type])) {
            self::$instances[$type] = self::createInstance($type);
        }

        return self::$instances[$type];
    }

    /**
     * @param string $type
     * @return Archive
     * @throws BaseException
     */
    private static function createInstance(string $type): Archive
    {
        if (!isset(self::ADAPTERS[$type])) {
            throw ArchiveException::adapterNotSupported($type);
        }

        $adapterClass = self::ADAPTERS[$type];

        return new Archive(new $adapterClass());
    }
}
