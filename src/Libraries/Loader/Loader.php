<?php

namespace Quantum\Loader;


class Loader
{

    private $module;
    private $env;
    private $fileName;
    private $hierarchical;
    private $filePath = null;

    public function __construct($repository, $hierarchical = true)
    {
        $this->module = $repository->module;
        $this->env = $repository->env;
        $this->fileName = $repository->fileName;
        $this->hierarchical = $hierarchical;
    }

    public function set($property, $value) {
        $this->$property = $value;
    }

    public function getFilePath() {
        $this->filePath = modules_dir() . DS . $this->module . DS . ucfirst($this->env) . $this->fileName . '.php';
        if (!file_exists($this->filePath)) {
            if ($this->hierarchical) {
                $this->filePath = base_dir() . DS . $this->env . DS . $this->fileName . '.php';
                if (!file_exists($this->filePath)) {
                    throw new \Exception($this->exceptionMessage);
                }
            }
        }

        return $this->filePath;
    }

    public function load()
    {
        return require_once $this->getFilePath();
    }

}