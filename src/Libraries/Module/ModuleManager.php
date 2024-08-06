<?php

namespace Quantum\Libraries\Module;

use Quantum\Di\Di;
use Quantum\Libraries\Storage\FileSystem;

class ModuleManager{

    protected $fs;
    protected $optionEnabled;

    private $moduleName;
    private $template;
    private $demo;
    private $modulePath;
    private $templatePath;

    function __construct(string $moduleName, string $template, string $demo, $enabled){
        $this->moduleName = $moduleName;

        $this->template = $template;
        
        $this->demo = $demo;

        $this->optionEnabled = $enabled;
        
        $type = $this->demo == "yes" ? "Demo" : "Default";

        $this->templatePath = __DIR__ . DS . "Templates" . DS . $type . DS . ucfirst($this->template);
        
        $this->modulePath = modules_dir() . DS . $this->moduleName;

        $this->fs = Di::get(FileSystem::class);
    }

    public function writeContents()
    {
        if (!$this->fs->isDirectory(modules_dir())) {
            $this->fs->makeDirectory(modules_dir());
        }
        $this->copyDirectoryWithTemplates($this->templatePath, $this->modulePath);
    }

    public function addModuleConfig()
    {
        $modulesConfigPath = base_dir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';
        $modules = require $modulesConfigPath;

        foreach ($modules['modules'] as $module => $options) {
            if ($module == $this->moduleName  || $options['prefix'] == strtolower($this->moduleName)) {
                throw new \Exception("A module or prefix named '$this->moduleName' already exists");
            }
        }

        $this->fs->put(
            $modulesConfigPath,
            str_replace(
                "'modules' => [",
                $this->writeModuleConfig($this->moduleName),
                $this->fs->get($modulesConfigPath)
            )
        );
    }

    private function copyDirectoryWithTemplates($src, $dst) {
        if (!$this->fs->isDirectory($src)) {
            throw new \Exception("Directory '$src' does not exist");
        }

        if (!$this->fs->isDirectory($dst)) {
            $this->fs->makeDirectory($dst);
        }

        $dir = $this->fs->listDirectory($src);

        foreach ($dir as $file) {
            $srcPath = $file;
            $dstPath = str_replace($src, $dst, $file);

            if ($this->fs->isDirectory($srcPath)) {
                $this->copyDirectoryWithTemplates($srcPath, $dstPath);
            } else {
                $processedContent = require_once $srcPath;
                $this->fs->put($dstPath, $processedContent);
            }
        }
    }

    /**
     * Add module to config
     * @param string $module
     * @return string
     */
    private function writeModuleConfig(string $module): string
    {
        $enabled = $this->optionEnabled ? "true" : "false";

        $prefix = $this->template == "web" && $this->demo == "yes" ? "" : strtolower($module);

        return "'modules' => [
        '" . $module . "' => [
            'prefix' => '" . $prefix . "',
            'enabled' => " . $enabled . ",
        ],";
    }
}