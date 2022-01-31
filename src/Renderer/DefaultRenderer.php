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
use Quantum\Di\Di;

class DefaultRenderer implements TemplateRenderer
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
     */
    public function render(string $view, array $params = [], array $configs = []): string
    {
        $fs = Di::get(FileSystem::class);

        $filePath = modules_dir() . DS . current_module() . DS . 'Views' . DS . $view . '.php';

        if (!$fs->exists($filePath)) {
            $filePath = base_dir() . DS . 'shared' . DS . 'views' . DS . $view . '.php';
            if (!$fs->exists($filePath)) {
                throw ViewException::fileNotFound($view);
            }
        }

        ob_start();
        ob_implicit_flush(0);

        if (!empty($params)) {
            extract($params, EXTR_OVERWRITE);
        }

        require $filePath;

        return ob_get_clean();
    }

}