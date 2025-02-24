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

use Symfony\Component\VarExporter\Exception\ExceptionInterface;
use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Router\Exceptions\ModuleLoaderException;
use Quantum\Exceptions\BaseException;
use Quantum\Environment\Environment;
use Quantum\Router\ModuleLoader;
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
    private $moduleName;

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
     * @var string
     */
    private $modulesConfigPath;

    /**
     * @var ModuleManager|null
     */
    private static $instance = null;

    /**
     * @param string $moduleName
     * @param string $template
     * @param string $demo
     * @param bool $enabled
     * @throws BaseException
     */
    private function __construct(string $moduleName, string $template, string $demo, bool $enabled)
    {
        $this->fs = FileSystemFactory::get();

        $this->moduleName = $moduleName;
        $this->optionEnabled = $enabled;
        $this->template = $template;
        $this->demo = $demo;

        $this->modulePath = modules_dir() . DS . $moduleName;
        $this->templatePath = $this->generateTemplatePath($template, $demo);
        $this->modulesConfigPath = base_dir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';
    }

    /**
     * @param string $moduleName
     * @param string $template
     * @param string $demo
     * @param bool $enabled
     * @return ModuleManager
     * @throws BaseException
     */
    public static function createInstance(string $moduleName, string $template, string $demo, bool $enabled): ModuleManager
    {
        if (self::$instance === null) {
            self::$instance = new self($moduleName, $template, $demo, $enabled);
        }

        return self::$instance;
    }

    /**
     * @return ModuleManager
     * @throws Exception
     */
    public static function getInstance(): ModuleManager
    {
        if (self::$instance === null) {
            throw new Exception("ModuleManager is not instantiated, call `createInstance()` first");
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * @return string
     */
    public function getBaseNamespace(): string
    {
        return Environment::getInstance()->getAppEnv() === 'testing'
            ? "Quantum\\Tests\\_root\\modules"
            : "Modules";
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
     * @throws ModuleLoaderException
     * @throws ExceptionInterface
     * @throws Exception
     */
    public function addModuleConfig()
    {
        $moduleConfigs = ModuleLoader::getInstance()->getModuleConfigs();

        foreach ($moduleConfigs as $module => $options) {
            if ($module == $this->moduleName || $options['prefix'] == strtolower($this->moduleName)) {
                throw new Exception("A module or prefix named '" . $this->moduleName . "' already exists");
            }
        }

        $moduleConfigs[$this->moduleName] = $this->getModuleOptions($this->moduleName);

        $this->updateModuleConfigFile($moduleConfigs);
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
                $content = $this->fs->get($srcPath);
                $processedContent = $this->replacePlaceholders($content);
                $this->fs->put($dstPath, $processedContent);
            }
        }
    }

    /**
     * @param string $content
     * @return string
     */
    private function replacePlaceholders(string $content): string
    {
        $placeholders = [
            '{{MODULE_NAMESPACE}}' => $this->getBaseNamespace() .'\\' . $this->getModuleName(),
            '{{MODULE_NAME}}' => $this->getModuleName(),
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $content);
    }

    /**
     * @param string $module
     * @return array
     */
    private function getModuleOptions(string $module): array
    {
        return [
            'prefix' => $this->template == "web" && $this->demo == "yes" ? "" : strtolower($module),
            'enabled' => $this->optionEnabled,
        ];
    }

    /**
     * @param array $moduleConfigs
     * @return void
     * @throws ExceptionInterface
     */
    private function updateModuleConfigFile(array $moduleConfigs): void
    {
        $this->fs->put(
            $this->modulesConfigPath,
            "<?php\n\nreturn " . export($moduleConfigs) . ";\n"
        );
    }

    /**
     * @param string $template
     * @param string $demo
     * @return string
     */
    private function generateTemplatePath(string $template, string $demo): string
    {
        $type = $demo === 'yes' ? 'Demo' : 'Default';
        return __DIR__ . DS . 'Templates' . DS . $type . DS . ucfirst($template);
    }
}