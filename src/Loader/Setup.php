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
 * @since 2.3.0
 */

namespace Quantum\Loader;


use Quantum\Exceptions\ConfigException;

/**
 * Class Setup
 * @package Quantum\Loader
 */
class Setup
{
    /**
     * @var bool
     */
    protected $hierarchical = false;

    /**
     * @var string|null
     */
    protected $module;

    /**
     * @var string|null
     */
    protected $env;

    /**
     * @var string|null
     */
    protected $fileName;

    /**
     * @var string|string
     */
    protected $exceptionMessage;

    /**
     * Setup constructor.
     * @param string|null $env
     * @param string|null $fileName
     * @param bool $hierarchical
     * @param string|null $module
     * @param string $exceptionMessage
     */
    public function __construct($env = null, $fileName = null, $hierarchical = false, $module = null, $exceptionMessage = ConfigException::CONFIG_FILE_NOT_FOUND)
    {
        $this->env = $env;
        $this->fileName = $fileName;
        $this->hierarchical = $hierarchical;
        $this->module = $module ?: current_module();
        $this->exceptionMessage = $exceptionMessage;
    }

    /**
     * Sets the env
     * @param string $env
     * @return $this
     */
    public function setEnv($env) {
        $this->env = $env;
        return $this;
    }

    /**
     * Gets the env
     * @return string|null
     */
    public function getEnv() {
        return $this->env;
    }

    /**
     * Set the filename
     * @param string $fileName
     * @return $this
     */
    public function setFilename($fileName) {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Gets the filename
     * @return string|null
     */
    public function getFilename() {
        return $this->fileName;
    }

    /**
     * Sets the module
     * @param string $module
     * @return $this
     */
    public function setModule($module) {
        $this->module = $module;
        return $this;
    }

    /**
     * Gets the module
     * @return string|null
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * Sets the exception message
     * @param string $exceptionMessage
     * @return $this
     */
    public function setExceptionMessage($exceptionMessage) {
        $this->exceptionMessage = $exceptionMessage;
        return $this;
    }

    /**
     * Gets the exception message
     * @return string
     */
    public function getExceptionMessage() {
        return $this->exceptionMessage;
    }

    /**
     * Set the hierarchy
     * @param bool $hierarchy
     * @return $this
     */
    public function setHierarchy($hierarchy) {
        $this->hierarchical = $hierarchy;
        return $this;
    }

    /**
     * Gets the hierarchy
     * @return bool
     */
    public function getHierarchy() {
        return $this->hierarchical;
    }
}