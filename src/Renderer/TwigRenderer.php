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
 * @since 2.6.0
 */

namespace Quantum\Renderer;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\ViewException;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Quantum\Di\Di;

class TwigRenderer implements TemplateRenderer
{

    /**
     * Renders the template
     * @param string $view
     * @param array $params
     * @param array $configs
     * @return string
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ViewException
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(string $view, array $params = [], array $configs = []): string
    {
        $fs = Di::get(FileSystem::class);

        if ($fs->exists(modules_dir() . DS . current_module() . DS . 'Views' . DS . $view . '.php')) {
            $loader = new FilesystemLoader(modules_dir() . DS . current_module() . DS . 'Views');
        } else if ($fs->exists(base_dir() . DS . 'base' . DS . 'views' . DS . $file . '.php')) {
            $loader = new FilesystemLoader(base_dir() . DS . 'base' . DS . 'views');
        } else {
            throw ViewException::fileNotFound($view);
        }
        
        $twig = new \Twig\Environment($loader, $configs);

        $definedFunctions = get_defined_functions();

        $allDefinedFunctions = array_merge($definedFunctions['internal'], $definedFunctions['user']);

        foreach ($allDefinedFunctions as $function) {
            if (function_exists($function)) {
                $twig->addFunction(new TwigFunction($function, $function));
            }
        }

        return $twig->render($view . '.php', $params);
    }

}