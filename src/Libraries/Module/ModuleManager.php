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
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Router\Exceptions\ModuleLoaderException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Environment\Environment;
use Quantum\Router\ModuleLoader;
use ReflectionException;
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
    private $modulePath;

    /**
     * @var string
     */
    private $assetsPath;

    /**
     * @var string
     */
    private $templatePath;

    /**
     * @var bool
     */
    private $withAssets;

    /**
     * @var string
     */
    private $modulesConfigPath;

    /**
     * @param string $moduleName
     * @param string $template
     * @param bool $enabled
     * @param bool $withAssets
     * @throws BaseException
     * @throws DiException
     * @throws ConfigException
     * @throws ReflectionException
     */
    public function __construct(string $moduleName, string $template, bool $enabled, bool $withAssets = false)
    {
        $this->fs = FileSystemFactory::get();

        $this->moduleName = $moduleName;

        $this->withAssets = $withAssets;
        $this->optionEnabled = $enabled;
        $this->template = $template;

        $this->assetsPath = assets_dir() . DS . $moduleName;
        $this->modulePath = modules_dir() . DS . $moduleName;
        $this->templatePath = __DIR__ . DS . 'Templates' . DS . ucfirst($template);
        $this->modulesConfigPath = base_dir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';
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
     * @throws Exception
     */
    public function writeContents()
    {
        $this->copyDirectoryWithTemplates("$this->templatePath" . DS . "src", $this->modulePath);

        if ($this->withAssets) {
            $this->copyAssets("$this->templatePath" . DS . "assets", $this->assetsPath);
        }
    }

    /**
     * @throws Exception
     */
    private function copyDirectoryWithTemplates(string $src, string $dst)
    {
        $this->copyDirectory($src, $dst, true);
    }

    /**
     * @throws Exception
     */
    private function copyAssets(string $src, string $dst)
    {
        $this->copyDirectory($src, $dst, false);
    }

    /**
     * @throws Exception
     */
    private function copyDirectory(string $src, string $dst, bool $processTemplates)
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
                $this->copyDirectory($srcPath, $dstPath, $processTemplates);
            } else {
                if ($processTemplates) {
                    $this->processTemplates($srcPath, $dstPath);
                }
                else {
                    $this->fs->copy($srcPath, $dstPath);
                }
            }
        }
    }

    /**
     * @param string $srcPath
     * @param string $dstPath
     */
    private function processTemplates(string $srcPath, string $dstPath){
        $dstPath = str_replace('.tpl', '.php', $dstPath);
        $content = $this->fs->get($srcPath);
        $processedContent = $this->replacePlaceholders($content);
        $this->fs->put($dstPath, $processedContent);
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
            'prefix' => $this->template == "DemoWeb" ? "" : strtolower($module),
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
}