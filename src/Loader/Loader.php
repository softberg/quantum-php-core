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
 * @since 2.5.0
 */

namespace Quantum\Loader;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\LoaderException;

/**
 * Class Loader
 * @package Quantum\Loader
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
     * @var \Quantum\Libraries\Storage\FileSystem
     */
    private $fs;

    /**
     * Class constructor
     * @param \Quantum\Libraries\Storage\FileSystem|null $fs
     */
    public function __construct(FileSystem $fs = null)
    {
        $this->fs = $fs;
    }

    /**
     * Setups the loader
     * @param Setup $setup
     * @return $this
     */
    public function setup(Setup $setup): Loader
    {
        $this->hierarchical = $setup->getHierarchy();
        $this->module = $setup->getModule();
        $this->env = $setup->getEnv();
        $this->fileName = $setup->getFilename();
        $this->exceptionMessage = $setup->getExceptionMessage();

        return $this;
    }

    /**
     * Sets new value
     * @param string $property
     * @param mixed $value
     * @return $this
     */
    public function set(string $property, $value): Loader
    {
        $this->$property = $value;
        return $this;
    }

    /**
     * Loads .php files from given directory
     * @param string $dir
     * @throws LoaderException
     */
    public function loadDir(string $dir)
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
    public function loadFile(string $path)
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
    public function getFilePath(): string
    {
        $filePath = modules_dir() . DS . $this->module . DS . ucfirst($this->env) . DS . $this->fileName . '.php';

        if (!$this->fs->exists($filePath)) {
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
