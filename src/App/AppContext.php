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

use Quantum\Di\Exceptions\DiException;
use Quantum\Environment\Environment;
use Quantum\App\Enums\AppType;
use Quantum\Di\DiContainer;
use Quantum\Config\Config;
use Quantum\Http\Response;
use Quantum\Http\Request;
use InvalidArgumentException;
use ReflectionException;
use RuntimeException;

/**
 * Class AppContext
 * @package Quantum\App
 */
class AppContext
{
    private string $mode;

    private string $baseDir;

    private ?DiContainer $container;

    public function __construct(string $mode, string $baseDir = '', ?DiContainer $container = null)
    {
        if (!in_array($mode, [AppType::WEB, AppType::CONSOLE], true)) {
            throw new InvalidArgumentException("Invalid app mode: $mode");
        }

        $this->mode = $mode;
        $this->baseDir = $baseDir;
        $this->container = $container;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    public function getContainer(): ?DiContainer
    {
        return $this->container;
    }

    public function isWebMode(): bool
    {
        return $this->mode === AppType::WEB;
    }

    public function isConsoleMode(): bool
    {
        return $this->mode === AppType::CONSOLE;
    }

    /**
     * @throws DiException|ReflectionException
     */
    public function getEnvironment(): Environment
    {
        return $this->resolveFromContainer(Environment::class);
    }

    /**
     * @throws DiException|ReflectionException
     */
    public function getConfig(): Config
    {
        return $this->resolveFromContainer(Config::class);
    }

    /**
     * @throws DiException|ReflectionException
     */
    public function getRequest(): Request
    {
        return $this->resolveFromContainer(Request::class);
    }

    /**
     * @throws DiException|ReflectionException
     */
    public function getResponse(): Response
    {
        return $this->resolveFromContainer(Response::class);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     * @throws DiException|ReflectionException
     */
    private function resolveFromContainer(string $class)
    {
        if ($this->container === null) {
            throw new RuntimeException('DiContainer is not set on AppContext.');
        }

        return $this->container->get($class);
    }
}
