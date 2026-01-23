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

namespace Quantum\Model;

use Quantum\Model\Exceptions\ModelException;

/**
 * Class Model
 * @package Quantum\Model
 */
abstract class Model
{
    /**
     * Internal attributes
     * @var array
     */
    protected array $attributes = [];

    /**
     * Models fillable properties
     * @var array
     */
    protected array $fillable = [];

    /**
     * Models hidden properties
     * Used by DBAL and plain models
     * @var array
     */
    public array $hidden = [];

    /**
     * @param string $key
     * @param $value
     * @return $this|mixed|null
     */
    public function prop(string $key, $value = null)
    {
        if (func_num_args() === 1) {
            return $this->attributes[$key] ?? null;
        }

        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Fill object properties
     * @param array $props
     * @return $this
     * @throws ModelException
     */
    public function fill(array $props): self
    {
        foreach ($props as $key => $value) {
            if (!$this->shouldFill($key)) {
                throw ModelException::inappropriateProperty($key);
            }

            $this->attributes[$key] = $value;
        }

        return $this;
    }

    /**
     * Converts to array
     * @return array
     */
    public function asArray(): array
    {
        if (!$this->hidden) {
            return $this->attributes;
        }

        return array_diff_key(
            $this->attributes,
            array_flip($this->hidden)
        );
    }

    /**
     * Checks if model is empty
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->asArray());
    }

    /**
     * @param string $key
     * @return $this|mixed|null
     */
    public function __get(string $key)
    {
        return $this->prop($key);
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function __set(string $key, $value): void
    {
        $this->prop($key, $value);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * @param string $key
     * @return void
     */
    public function __unset(string $key): void
    {
        unset($this->attributes[$key]);
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function shouldFill(string $key): bool
    {
        return in_array($key, $this->fillable, true);
    }
}
