<?php

namespace Quantum\Tests\Helpers;

use Psr\SimpleCache\CacheInterface;

class InMemoryPsrCache implements CacheInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $items = [];

    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->items) ? $this->items[$key] : $default;
    }

    public function set($key, $value, $ttl = null): bool
    {
        $this->items[$key] = $value;
        return true;
    }

    public function delete($key): bool
    {
        unset($this->items[$key]);
        return true;
    }

    public function clear(): bool
    {
        $this->items = [];
        return true;
    }

    public function getMultiple($keys, $default = null)
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function has($key): bool
    {
        return array_key_exists($key, $this->items);
    }
}
