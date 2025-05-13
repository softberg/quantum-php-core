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
 * @since 2.9.7
 */

namespace Quantum\Renderer\Adapters;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Renderer\Contracts\TemplateRendererInterface;
use Quantum\Renderer\Exceptions\RendererException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Di\Exceptions\DiException;
use ReflectionException;

/**
 * Class HtmlAdapter
 * @package Quantum\Renderer
 */
class HtmlAdapter implements TemplateRendererInterface
{

    /**
     * @var FileSystem
     */
    protected $fs;

    /**
     * @var array|null
     */
    protected $configs;

    /**
     * @param array|null $configs
     * @throws DiException
     * @throws ReflectionException
     */
    public function __construct(?array $configs = [])
    {
        $this->configs = $configs;

        $this->fs = FileSystemFactory::get();
    }

    /**
     * Renders the view
     * @param string $view
     * @param array $params
     * @return string
     * @throws BaseException
     */
    public function render(string $view, array $params = []): string
    {
        $filePath = $this->getViewFilePath($view);

        if (!$this->fs->exists($filePath)) {
            throw RendererException::fileNotFound($view);
        }

        ob_start();
        ob_implicit_flush(0);

        if (!empty($params)) {
            extract($params);
        }

        require $filePath;

        return ob_get_clean();
    }

    /**
     * @param string $view
     * @return string
     */
    private function getViewFilePath(string $view): string
    {
        $moduleViewPath = modules_dir() . DS . current_module() . DS . 'Views' . DS . $view . '.php';
        $sharedViewPath = base_dir() . DS . 'shared' . DS . 'views' . DS . $view . '.php';

        return $this->fs->exists($moduleViewPath) ? $moduleViewPath : $sharedViewPath;
    }
}