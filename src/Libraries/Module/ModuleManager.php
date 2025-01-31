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

namespace Quantum\Libraries\Module;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Exceptions\BaseException;
use Exception;

class ModuleManager
{
    /**
     * @var mixed
     */
    protected $fs;

    /**
     * @var bool
     */
    protected $optionEnabled;

    /**
     * @var string
     */
    public static $moduleName;

    /**
     * @var string
     */
    private $template;

    /**
     * @var string
     */
    private $demo;

    /**
     * @var string
     */
    private $modulePath;

    /**
     * @var string
     */
    private $templatePath;

    /**
     * @param string $moduleName
     * @param string $template
     * @param string $demo
     * @param bool $enabled
     * @throws BaseException
     */
    function __construct(string $moduleName, string $template, string $demo, bool $enabled)
    {
        self::$moduleName = $moduleName;

        $this->template = $template;

        $this->demo = $demo;

        $this->optionEnabled = $enabled;

        $type = $this->demo == "yes" ? "Demo" : "Default";

        $this->templatePath = __DIR__ . DS . "Templates" . DS . $type . DS . ucfirst($this->template);

        $this->modulePath = modules_dir() . DS . self::$moduleName;

        $this->fs = FileSystemFactory::get();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function writeContents()
    {
        if (!$this->fs->isDirectory(modules_dir())) {
            $this->fs->makeDirectory(modules_dir());
        }

        $this->copyDirectoryWithTemplates($this->templatePath, $this->modulePath);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function addModuleConfig()
    {
        $modulesConfigPath = base_dir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';
        $modules = $this->fs->require($modulesConfigPath);

        foreach ($modules['modules'] as $module => $options) {
            if ($module == self::$moduleName || $options['prefix'] == strtolower(self::$moduleName)) {
                throw new Exception("A module or prefix named '" . self::$moduleName . "' already exists");
            }
        }

        $this->fs->put(
            $modulesConfigPath,
            str_replace(
                "'modules' => [",
                $this->writeModuleConfig(self::$moduleName),
                $this->fs->get($modulesConfigPath)
            )
        );
    }

    /**
     * @param string $src
     * @param string $dst
     * @return void
     * @throws Exception
     */
    private function copyDirectoryWithTemplates(string $src, string $dst)
    {
        if (!$this->fs->isDirectory($src)) {
            throw new Exception("Directory '$src' does not exist");
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
                $processedContent = $this->fs->require($srcPath);
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