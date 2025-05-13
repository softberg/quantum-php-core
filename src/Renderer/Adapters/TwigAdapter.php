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
use Twig\Loader\FilesystemLoader;
use Twig\Error\RuntimeError;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\TwigFunction;
use Twig\Environment;

/**
 * Class TwigAdapter
 * @package Quantum\Renderer
 */
class TwigAdapter implements TemplateRendererInterface
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
     * @throws BaseException
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $view, array $params = []): string
    {
        $loader = $this->getLoader($view);

        $twig = new Environment($loader, $this->configs);

        $this->addFunctionsToTwig($twig);

        return $twig->render($view . '.php', $params);
    }

    /**
     * @param string $view
     * @return FilesystemLoader
     * @throws BaseException
     */
    private function getLoader(string $view): FilesystemLoader
    {
        $moduleViewPath = modules_dir() . DS . current_module() . DS . 'Views' . DS . $view . '.php';
        $sharedViewPath = base_dir() . DS . 'shared' . DS . 'views' . DS . $view . '.php';

        if ($this->fs->exists($moduleViewPath)) {
            return new FilesystemLoader(dirname($moduleViewPath));
        }

        if ($this->fs->exists($sharedViewPath)) {
            return new FilesystemLoader(dirname($sharedViewPath));
        }

        throw RendererException::fileNotFound($view);
    }

    /**
     * @param Environment $twig
     */
    private function addFunctionsToTwig(Environment $twig)
    {
        $definedFunctions = get_defined_functions();
        $allDefinedFunctions = array_merge($definedFunctions['internal'], $definedFunctions['user']);

        foreach ($allDefinedFunctions as $function) {
            if (function_exists($function)) {
                $twig->addFunction(new TwigFunction($function, $function));
            }
        }
    }
}