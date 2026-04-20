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

namespace Quantum\Cookie;

use Quantum\Cookie\Contracts\CookieStorageInterface;
use Quantum\App\Exceptions\BaseException;

/**
 * Class Cookie
 * @package Quantum\Cookie
 */
class Cookie implements CookieStorageInterface
{
    /**
     * Cookie storage
     * @var array<string, mixed>
     */
    private array $storage;

    /**
     * @param array<string, mixed> $storage
     */
    public function __construct(array &$storage)
    {
        $this->storage = &$storage;
    }

    /**
     * @inheritDoc
     * @throws BaseException
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $allCookies = [];

        foreach ($this->storage as $key => $value) {
            $allCookies[$key] = crypto_decode($value);
        }

        return $allCookies;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return isset($this->storage[$key]) && !empty($this->storage[$key]);
    }

    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function get(string $key)
    {
        return $this->has($key) ? crypto_decode($this->storage[$key]) : null;
    }

    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function set(string $key, $value = '', int $time = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): void
    {
        $this->storage[$key] = crypto_encode($value);
        setcookie($key, crypto_encode($value), ['expires' => $time !== 0 ? time() + $time : $time, 'path' => $path, 'domain' => $domain, 'secure' => $secure, 'httponly' => $httponly]);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key, string $path = '/'): void
    {
        if ($this->has($key)) {
            unset($this->storage[$key]);
            setcookie($key, '', ['expires' => time() - 3600, 'path' => $path]);
        }
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        if (count($this->storage)) {
            foreach (array_keys($this->storage) as $key) {
                $this->delete($key, '/');
            }
        }
    }
}
