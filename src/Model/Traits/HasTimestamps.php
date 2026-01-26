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

namespace Quantum\Model\Traits;

/**
 * Trait HasTimestamps
 *
 * @property array $attributes
 * @property string $idColumn
 */
trait HasTimestamps
{
    /**
     * Column name for created timestamp
     * @var string
     */
    public string $createdAt = 'created_at';

    /**
     * Column name for updated timestamp
     * @var string
     */
    public string $updatedAt = 'updated_at';

    /**
     * Timestamp storage type: datetime|unix
     * @var string
     */
    public string $timestampType = 'datetime';

    /**
     * Determine whether the current model is new (insert) or existing (update).
     * @return bool
     */
    protected function isNewRecord(): bool
    {
        $id = $this->attributes[$this->idColumn] ?? null;

        return $id === null || $id === '';
    }

    /**
     * Returns the current timestamp value based on model config.
     * Supports: datetime|unix
     * @return int|string
     */
    protected function nowTimestampValue()
    {
        if ($this->getTimestampType() === 'unix') {
            return time();
        }

        return date('Y-m-d H:i:s');
    }

    /**
     * Get timestamp type.
     * @return string
     */
    protected function getTimestampType(): string
    {
        if (defined(static::class . '::TIMESTAMP_TYPE')) {
            return static::TIMESTAMP_TYPE;
        }

        return $this->timestampType;
    }

    /**
     * Get "created at" column name.
     * @return string
     */
    protected function getCreatedAtColumn(): string
    {
        if (defined(static::class . '::CREATED_AT')) {
            return static::CREATED_AT;
        }

        return $this->createdAt;
    }

    /**
     * Get "updated at" column name.
     * @return string
     */
    protected function getUpdatedAtColumn(): string
    {
        if (defined(static::class . '::UPDATED_AT')) {
            return static::UPDATED_AT;
        }

        return $this->updatedAt;
    }

    /**
     * Touch timestamps on save
     * @return void
     */
    protected function touchTimestamps(): void
    {
        $now = $this->nowTimestampValue();

        $createdAt = $this->getCreatedAtColumn();
        $updatedAt = $this->getUpdatedAtColumn();

        if ($this->isNewRecord()) {
            if (!array_key_exists($createdAt, $this->attributes)) {
                $this->attributes[$createdAt] = $now;
            }
        }

        $this->attributes[$updatedAt] = $now;
    }
}
