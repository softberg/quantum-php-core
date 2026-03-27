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
    private static ?string $baseDir = null;

    private AppInterface $adapter;

    public function __construct(AppInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public static function setBaseDir(string $baseDir): void
    {
        self::$baseDir = $baseDir;
    }

    public static function getBaseDir(): string
    {
        return self::$baseDir ?? '';
    }

    public function getAdapter(): AppInterface
    {
        return $this->adapter;
    }

    /**
     * @param array<mixed>|null $arguments
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
