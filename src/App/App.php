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

namespace Quantum\App;

use Quantum\App\Exceptions\BaseException;
use Quantum\App\Exceptions\AppException;
use Quantum\App\Contracts\AppInterface;

/**
 * Class App
 * @package Quantum\App
 */
class App
{
    /**
     * Web app adapter
     */
    public const WEB = 'web';

    /**
     * Console app adapter
     */
    public const CONSOLE = 'console';

    /**
     * @var string
     */
    private static $baseDir;

    /**
     * @var AppInterface
     */
    private $adapter;

    /**
     * @param AppInterface $adapter
     */
    public function __construct(AppInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param string $baseDir
     */
    public static function setBaseDir(string $baseDir)
    {
        self::$baseDir = $baseDir;
    }

    /**
     * @return string
     */
    public static function getBaseDir(): string
    {
        return self::$baseDir;
    }

    /**
     * @return AppInterface
     */
    public function getAdapter(): AppInterface
    {
        return $this->adapter;
    }

    /**
     * @param string $method
     * @param array|null $arguments
     * @return mixed
     * @throws BaseException
     */
    public function __call(string $method, ?array $arguments)
    {
        if (!method_exists($this->adapter, $method)) {
            throw AppException::methodNotSupported($method, get_class($this->adapter));
        }

        return $this->adapter->$method(...$arguments);
    }
}
