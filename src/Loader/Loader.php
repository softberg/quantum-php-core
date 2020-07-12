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

namespace Quantum\Loader;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\LoaderException;

/**
 * Loader Class
 * @package Quantum
 * @category Loader
 */
class Loader
{

    /**
     * Current module
     * @var string
     */
    private $module;

    /**
     * Environment
     * @var string
     */
    private $env;

    /**
     * File name
     * @var string
     */
    private $fileName;

    /**
     * Hierarchical
     * @var bool
     */
    private $hierarchical;

    /**
     * Exception message
     */
    private $exceptionMessage;

    /**
     * File System
     * @var Quantum\Libraries\Storage\FileSystem
     */
    private $fs;

    /**
     * Loader constructor.
     * @param object $repository
     * @param bool $hierarchical
     */
    public function __construct($hierarchical = true)
    {
        $this->fs = new FileSystem();
        $this->hierarchical = $hierarchical;
    }

    /**
     * Setups the loader
     * @param object $repository
     * @return $this
     */
    public function setup($repository)
    {
        $this->module = $repository->module;
        $this->env = $repository->env;
        $this->fileName = $repository->fileName;
        $this->exceptionMessage = $repository->exceptionMessage;

        return $this;
    }

    /**
     * Sets new value
     * @param string $property
     * @param mixed $value
     */
    public function set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Loads .php files from given directory
     * @param string $dir
     */
    public function loadDir($dir)
    {
        foreach ($this->fs->glob($dir . DS . "*.php") as $filename) {
            $this->loadFile($filename);
        }
    }

    /**
     * Loads .php file
     * @param string $path
     * @throws LoaderException
     */
    public function loadFile($path)
    {
        if (!$this->fs->exists($path)) {
            throw new LoaderException(_message($this->exceptionMessage, $path));
        }

        require_once $path;
    }

    /**
     * Loads the content
     * @return mixed
     * @throws LoaderException
     */
    public function load()
    {
        return require $this->getFilePath();
    }

    /**
     * Gets the file path
     * @return string
     * @throws LoaderException
     */
    private function getFilePath()
    {
        $filePath = modules_dir() . DS . $this->module . DS . ucfirst($this->env) . DS . $this->fileName . '.php';

        if (!file_exists($filePath)) {
            if ($this->hierarchical) {
                $filePath = base_dir() . DS . $this->env . DS . $this->fileName . '.php';
                if (!$this->fs->exists($filePath)) {
                    throw new LoaderException(_message($this->exceptionMessage, $this->fileName));
                }
            } else {
                throw new LoaderException(_message($this->exceptionMessage, $this->fileName));
            }
        }

        return $filePath;
    }

}
