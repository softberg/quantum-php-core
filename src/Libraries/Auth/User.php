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

namespace Quantum\Libraries\Auth;

/**
 * Class User
 * @package Quantum\Libraries\Auth
 */
class User
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Set Data
     * @param array $data
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get Data
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get Fields
     * @return array
     */
    public function getFields(): array
    {
        return array_keys($this->data);
    }

    /**
     * Set Fields
     * @param array $schema
     * @return $this
     */
    public function setFields(array $schema): self
    {
        foreach ($schema as $field) {
            if (isset($field['name'])) {
                $this->data[$field['name']] = '';
            }
        }

        return $this;
    }

    /**
     * Get Field Value
     * @param string $field
     * @return string|null
     */
    public function getFieldValue(string $field): ?string
    {
        return $this->hasField($field) ? $this->data[$field] : null;
    }

    /**
     * Set Fields Value
     * @param string $field
     * @param string|null $value
     * @return $this
     */
    public function setFieldValue(string $field, ?string $value): self
    {
        $this->data[$field] = $value;
        return $this;
    }

    /**
     * Has Field
     * @param string $field
     * @return bool
     */
    public function hasField(string $field): bool
    {
        return array_key_exists($field, $this->data);
    }

    /**
     * Gets the user property
     * @param string $property
     * @return string|null
     */
    public function __get(string $property)
    {
        return $this->getFieldValue($property);
    }

}
