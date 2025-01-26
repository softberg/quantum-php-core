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
 * @since 2.9.5
 */

namespace Quantum\Loader;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Loader\Exceptions\LoaderException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\BaseException;

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
    private $pathPrefix;

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
     * @var string
     */
    private $exceptionMessage;

    /**
     * File System
     * @var FileSystem
     */
    private $fs;

    /**
     * Loader constructor
     * @throws BaseException
     */
    public function __construct()
    {
        $this->fs = FileSystemFactory::get();
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
        $this->pathPrefix = $setup->getPathPrefix();
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
     */
    public function loadDir(string $dir)
    {
        foreach (glob($dir . DS . "*.php") as $filename) {
            require_once $filename;
        }
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
        $filePath = '';

        if ($this->module) {
            $filePath = modules_dir() . DS . $this->module . DS;
        }

        if ($this->pathPrefix) {
            $filePath .= $this->pathPrefix . DS;
        }

        $filePath .= $this->fileName . '.php';

        if (!$this->fs->exists($filePath)) {
            if ($this->hierarchical) {
                $filePath = base_dir() . DS . 'shared' . DS . strtolower($this->pathPrefix) . DS . $this->fileName . '.php';

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
