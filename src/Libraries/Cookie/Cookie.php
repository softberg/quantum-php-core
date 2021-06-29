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
 * @since 2.0.0
 */

namespace Quantum\Libraries\Cookie;

use Quantum\Libraries\Encryption\Cryptor;

/**
 * Class Cookie
 * @package Quantum\Libraries\Cookie
 */
class Cookie implements CookieStorageInterface
{

    /**
     * Cookie storage
     * @var array $storage
     */
    private $storage = [];

    /**
     * Cryptor instance
     * @var \Quantum\Libraries\Encryption\Cryptor
     */
    private $cryptor;

    /**
     * Cookie constructor.
     * @param array $storage
     * @param \Quantum\Libraries\Encryption\Cryptor $cryptor
     */
    public function __construct(array &$storage, Cryptor $cryptor)
    {
        $this->storage = &$storage;
        $this->cryptor = $cryptor;
    }

    /**
     * Gets all data
     * @return array
     * @throws \Quantum\Exceptions\CryptorException
     */
    public function all()
    {
        $allCookies = [];

        foreach ($this->storage as $key => $value) {
            $allCookies[$key] = $this->decode($value);
        }

        return $allCookies;
    }

    /**
     * Check if storage contains a data by given key
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->storage[$key]) && !empty($this->storage[$key]);
    }

    /**
     * Gets data by given key
     * @param string $key
     * @return string|null
     * @throws \Quantum\Exceptions\CryptorException
     */
    public function get(string $key): ?string
    {
        return $this->has($key) ? $this->decode($this->storage[$key]) : null;
    }

    /**
     * Sets data by given key
     * @param string $key
     * @param string $value
     * @param int $time
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @return \Quantum\Contracts\StorageInterface|\Quantum\Libraries\Cookie\CookieStorageInterface|void
     * @throws \Quantum\Exceptions\CryptorException
     */
    public function set(string $key, $value = '', int $time = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
    {
        $this->storage[$key] = $this->encode($value);
        setcookie($key, $this->encode($value), $time ? time() + $time : $time, $path, $domain, $secure, $httponly);
    }

    /**
     * Deletes data by given key
     * @param string $key
     * @param string $path
     * @return void
     */
    public function delete(string $key, string $path = '/')
    {
        if ($this->has($key)) {
            unset($this->storage[$key]);
            setcookie($key, '', time() - 3600, $path);
        }
    }

    /**
     * Deletes whole cookie data
     */
    public function flush()
    {
        if (count($this->storage)) {
            foreach ($this->storage as $key => $value) {
                $this->delete($key, '/');
            }
        }
    }

    /**
     * Encodes the cookie data
     * @param mixed $value
     * @return string
     * @throws \Quantum\Exceptions\CryptorException
     */
    private function encode($value): string
    {
        $value = (is_array($value) || is_object($value)) ? serialize($value) : $value;
        return $this->cryptor->encrypt($value);
    }

    /**
     * Decodes the cookie data
     * @param string $value
     * @return mixed
     * @throws \Quantum\Exceptions\CryptorException
     */
    private function decode(string $value)
    {
        if (empty($value)) {
            return $value;
        }

        $decrypted = $this->cryptor->decrypt($value);

        if ($data = @unserialize($decrypted)) {
            $decrypted = $data;
        }

        return $decrypted;
    }

}
