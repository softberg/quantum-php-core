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

namespace Quantum\RateLimit\Factories;

use Quantum\RateLimit\Contracts\RateLimitAdapterInterface;
use Quantum\RateLimit\Adapters\RedisRateLimitAdapter;
use Quantum\RateLimit\Adapters\FileRateLimitAdapter;
use Quantum\RateLimit\Exceptions\RateLimitException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\RateLimit\Enums\RateLimitType;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\RateLimit\RateLimiter;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Di\Di;

class RateLimiterFactory
{
    public const ADAPTERS = [
        RateLimitType::FILE => FileRateLimitAdapter::class,
        RateLimitType::REDIS => RedisRateLimitAdapter::class,
    ];

    /**
     * @var array<string, RateLimiter>
     */
    private array $instances = [];

    /**
     * @throws ConfigException|BaseException|DiException|ReflectionException
     */
    public static function get(?string $adapter = null): RateLimiter
    {
        if (!Di::isRegistered(self::class)) {
            Di::register(self::class);
        }

        return Di::get(self::class)->resolve($adapter);
    }

    /**
     * @throws ConfigException|BaseException|DiException|ReflectionException
     */
    public function resolve(?string $adapter = null): RateLimiter
    {
        if (!config()->has('rate_limit')) {
            config()->import(new Setup('config', 'rate_limit'));
        }

        $adapter ??= (string) config()->get('rate_limit.default', RateLimitType::FILE);

        if (!isset($this->instances[$adapter])) {
            $this->instances[$adapter] = new RateLimiter(
                $this->createAdapter($adapter)
            );
        }

        return $this->instances[$adapter];
    }

    /**
     * @throws BaseException
     */
    private function createAdapter(string $adapter): RateLimitAdapterInterface
    {
        $class = self::ADAPTERS[$adapter] ?? null;

        if ($class === null) {
            throw RateLimitException::adapterNotSupported($adapter);
        }

        return new $class();
    }
}
