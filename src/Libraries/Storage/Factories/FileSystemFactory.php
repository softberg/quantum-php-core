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
 * @since 2.9.9
 */

namespace Quantum\Libraries\Storage\Factories;

use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\Local\LocalFileSystemAdapter;
use Quantum\Libraries\Storage\Adapters\GoogleDrive\GoogleDriveApp;
use Quantum\Libraries\Storage\Contracts\TokenServiceInterface;
use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxApp;
use Quantum\Libraries\Storage\Contracts\CloudAppInterface;
use Quantum\Service\Exceptions\ServiceException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Service\Factories\ServiceFactory;
use Quantum\Libraries\HttpClient\HttpClient;
use Quantum\App\Exceptions\BaseException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Di\Exceptions\DiException;
use Quantum\Loader\Setup;
use ReflectionException;

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
     * Supported apps
     */
    const APPS = [
        FileSystem::DROPBOX => DropboxApp::class,
        FileSystem::GDRIVE => GoogleDriveApp::class,
    ];

    /**
     * @var array
     */
    private static $instances = [];

    /**
     * @param string|null $adapter
     * @return FileSystem
     * @throws BaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws ConfigException
     */
    public static function get(?string $adapter = null): FileSystem
    {
        if (!config()->has('fs')) {
            config()->import(new Setup('config', 'fs'));
        }

        $adapter = $adapter ?? config()->get('fs.default');

        $adapterClass = self::getAdapterClass($adapter);

        if (!isset(self::$instances[$adapter])) {
            self::$instances[$adapter] = self::createInstance($adapterClass, $adapter);
        }

        return self::$instances[$adapter];
    }

    /**
     * @param string $adapterClass
     * @param string $adapter
     * @return FileSystem
     * @throws BaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws ServiceException
     */
    private static function createInstance(string $adapterClass, string $adapter): FileSystem
    {
        return new FileSystem(new $adapterClass(
            self::createCloudApp($adapter)
        ));
    }

    /**
     * @param string $adapter
     * @return string
     * @throws BaseException
     */
    private static function getAdapterClass(string $adapter): string
    {
        if (!array_key_exists($adapter, self::ADAPTERS)) {
            throw FileSystemException::adapterNotSupported($adapter);
        }

        return self::ADAPTERS[$adapter];
    }

    /**
     * @param string $adapter
     * @return CloudAppInterface|null
     * @throws BaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws ServiceException
     */
    private static function createCloudApp(string $adapter): ?CloudAppInterface
    {
        if ($adapter === FileSystem::LOCAL || !isset(self::APPS[$adapter])) {
            return null;
        }

        $cloudAppClass = self::APPS[$adapter];

        return new $cloudAppClass(
            config()->get('fs.' . $adapter . '.params.app_key'),
            config()->get('fs.' . $adapter . '.params.app_secret'),
            self::createTokenService($adapter),
            new HttpClient()
        );
    }

    /**
     * @param string $adapter
     * @return TokenServiceInterface
     * @throws BaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws ServiceException
     */
    private static function createTokenService(string $adapter): TokenServiceInterface
    {
        $serviceClass = config()->get('fs.' . $adapter . '.service');

        $tokenService = ServiceFactory::create($serviceClass);

        if (!$tokenService instanceof TokenServiceInterface) {
            throw FileSystemException::notInstanceOf($serviceClass, TokenServiceInterface::class);
        }

        return $tokenService;
    }
}