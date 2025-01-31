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

namespace Quantum\Libraries\Storage\Factories;

use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Local\LocalFileSystemAdapter;
use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Libraries\Storage\Contracts\CloudAppInterface;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\BaseException;

/**
 * Class FileSystemFactory
 * @package Quantum\Libraries\Storage
 */
class FileSystemFactory
{

    /**
     * Supported adapters
     */
    const ADAPTERS = [
        FileSystem::LOCAL => LocalFileSystemAdapter::class,
        FileSystem::DROPBOX => DropboxFileSystemAdapter::class,
        FileSystem::GDRIVE => GoogleDriveFileSystemAdapter::class,
    ];

    /**
     * @var array
     */
    private static $instances = [];

    /**
     * @param string $type
     * @param CloudAppInterface|null $cloudApp
     * @return FileSystem
     * @throws BaseException
     */
    public static function get(string $type = FileSystem::LOCAL, ?CloudAppInterface $cloudApp = null): FileSystem
    {
        if (!isset(self::$instances[$type])) {
            self::$instances[$type] = self::createInstance($type, $cloudApp);
        }

        return self::$instances[$type];
    }

    /**
     * @param string $type
     * @param CloudAppInterface|null $cloudApp
     * @return FileSystem
     * @throws BaseException
     */
    private static function createInstance(string $type, ?CloudAppInterface $cloudApp = null): FileSystem
    {
        if (!isset(self::ADAPTERS[$type])) {
            throw FileSystemException::adapterNotSupported($type);
        }

        $adapterClass = self::ADAPTERS[$type];

        return new FileSystem(new $adapterClass($cloudApp));
    }
}