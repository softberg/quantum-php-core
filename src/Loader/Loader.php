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
 * @since 1.5.0
 */

namespace Quantum\Loader;

use Quantum\Libraries\Storage\FileSystem;

/**
 * Loader Class
 *
 * @package Quantum
 * @category Loader
 */
class Loader
{

    /**
     * Current module
     *
     * @var string
     */
    private $module;

    /**
     * Environment
     *
     * @var string
     */
    private $env;

    /**
     * File name
     *
     * @var string
     */
    private $fileName;

    /**
     * Hierarchical
     *
     * @var bool
     */
    private $hierarchical;

    /**
     * Exception message
     *
     * @var string
     */
    private $exceptionMessage;

    /**
     * File path to load
     * @var string
     */
    private $filePath = null;

    /**
     * Loader constructor.
     *
     * @param object $repository
     * @param bool $hierarchical
     */
    public function __construct($repository, $hierarchical = true)
    {
        $this->module = $repository->module;
        $this->env = $repository->env;
        $this->fileName = $repository->fileName;
        $this->hierarchical = $hierarchical;
        $this->exceptionMessage = $repository->exceptionMessage;
    }

    /**
     * Set new value
     *
     * @param string $property
     * @param mixed $value
     */
    public function set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Get File Path
     *
     * @return string
     * @throws \Exception
     */
    public function getFilePath()
    {
        $this->filePath = modules_dir() . DS . $this->module . DS . ucfirst($this->env) . DS . $this->fileName . '.php';
        if (!file_exists($this->filePath)) {
            if ($this->hierarchical) {
                $this->filePath = base_dir() . DS . $this->env . DS . $this->fileName . '.php';
                if (!file_exists($this->filePath)) {
                    throw new \Exception(_message($this->exceptionMessage, $this->fileName));
                }
            } else {
                throw new \Exception(_message($this->exceptionMessage, $this->fileName));
            }
        }

        return $this->filePath;
    }

    /**
     * Load
     *
     * @return mixed
     * @throws \Exception
     */
    public function load()
    {
        return require $this->getFilePath();
    }

    /**
     * 
     * LoadFiles
     * 
     * Loads .php files from given directory
     * 
     * @param string $path
     */
    public static function loadFiles($path)
    {

        $fileSystem = new FileSystem();

        foreach ($fileSystem->glob($path . DS . "*.php") as $filename) {
            require_once $filename;
        }
    }

}
