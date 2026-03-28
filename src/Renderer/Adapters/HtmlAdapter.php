<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Renderer\Adapters;

use Quantum\Renderer\Contracts\TemplateRendererInterface;
use Quantum\Renderer\Exceptions\RendererException;
use Quantum\Storage\Factories\FileSystemFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Storage\FileSystem;
use ReflectionException;

/**
 * Class HtmlAdapter
 * @package Quantum\Renderer
 */
class HtmlAdapter implements TemplateRendererInterface
{
    protected FileSystem $fs;

    /**
     * @var array<string, mixed>|null
     */
    protected ?array $configs;

    /**
     * @param array<string, mixed>|null $configs
     * @throws BaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws ConfigException
     */
    public function __construct(?array $configs = [])
    {
        $this->configs = $configs;

        $this->fs = FileSystemFactory::get();
    }

    /**
     * Renders the view
     * @param array<string, mixed> $params
     * @throws BaseException|ReflectionException
     */
    public function render(string $view, array $params = []): string
    {
        $filePath = $this->getViewFilePath($view);

        if (!$this->fs->exists($filePath)) {
            throw RendererException::fileNotFound($view);
        }

        ob_start();
        /** @phpstan-ignore argument.type */
        ob_implicit_flush(PHP_VERSION_ID >= 80000 ? false : 0);

        if ($params !== []) {
            extract($params);
        }

        require $filePath;

        $content = ob_get_clean();

        return $content !== false ? $content : '';
    }

    /**
     * @throws DiException
     * @throws ReflectionException
     */
    private function getViewFilePath(string $view): string
    {
        $moduleViewPath = modules_dir() . DS . current_module() . DS . 'Views' . DS . $view . '.php';
        $sharedViewPath = base_dir() . DS . 'shared' . DS . 'views' . DS . $view . '.php';

        return $this->fs->exists($moduleViewPath) ? $moduleViewPath : $sharedViewPath;
    }
}
