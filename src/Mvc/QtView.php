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
 * @since 2.0.0
 */

namespace Quantum\Mvc;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\ViewException;
use Quantum\Factory\ViewFactory;
use Quantum\Hooks\HookManager;
use Quantum\Debugger\Debugger;
use Quantum\Di\Di;
use Error;

/**
 * Base View Class
 *
 * QtView class is a base class that responsible for rendering and
 * outputting the view
 *
 * @package Quantum
 * @category MVC
 */
class QtView
{

    /**
     * Layout file
     * @var string
     */
    private $layout = null;

    /**
     * Rendered view
     * @var string
     */
    private $view = null;

    /**
     * Rendered debug bar
     * @var string|null 
     */
    private $debugBar = null;

    /**
     * View data
     * @var array
     */
    private $data = [];

    /**
     * QtView constructor.
     * @throws ViewException
     */
    public function __construct()
    {
        if (get_caller_class() != ViewFactory::class) {
            throw new ViewException(_message(ExceptionMessages::DIRECT_VIEW_INCTANCE, [ViewFactory::class]));
        }
    }

    /**
     * Sets a layout
     * @param string $layout
     * @return void
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Gets the layout
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Sets view parameter
     * @param string $key
     * @param mixed $value
     */
    public function setParam(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Gets the view parameter
     * @param string $key
     * @return mixed|null
     */
    public function getParam(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Sets multiple view parameters 
     * @param array $params
     */
    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
    }

    /**
     * Gets all view parameters
     * @return array
     */
    public function getParams(): array
    {
        return $this->data;
    }

    /**
     * Renders the view
     * @param string $view
     * @param array $params
     * @return string|null
     * @throws ViewException
     */
    public function render($view, $params = []): ?string
    {
        if (!$this->layout) {
            throw new ViewException(ExceptionMessages::LAYOUT_NOT_SET);
        }

        if (!empty($params)) {
            $this->data = array_merge($this->data, $params);
        }

        $this->view = $this->renderFile($view);

        if (filter_var(config()->get('debug'), FILTER_VALIDATE_BOOLEAN)) {
            $this->debugBar = $this->renderDebugBar($view);
        }

        return $this->renderFile($this->layout);
    }

    /**
     * Renders partial view
     * @param string $view
     * @param array $params
     * @return string|null
     * @throws ViewException
     */
    public function renderPartial($view, $params = []): ?string
    {
        if (!empty($params)) {
            $this->data = array_merge($this->data, $params);
        }

        return $this->renderFile($view);
    }

    /**
     * Gets the rendered debugbar
     * @return string
     */
    public function getDebugbar()
    {
        return $this->debugBar;
    }

    /**
     * Gets the rendered view
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Finds a given file
     * @param string $file
     * @return string
     * @throws ViewException
     */
    private function findFile($file)
    {
        $fs = Di::get(FileSystem::class);

        $filePath = modules_dir() . DS . current_module() . DS . 'Views' . DS . $file . '.php';

        if (!$fs->exists($filePath)) {
            $filePath = base_dir() . DS . 'base' . DS . 'views' . DS . $file . '.php';
            if (!$fs->exists($filePath)) {
                throw new ViewException(_message(ExceptionMessages::VIEW_FILE_NOT_FOUND, $file));
            }
        }

        return $filePath;
    }

    /**
     * Renders the view
     * @param string $view
     * @param array $parmas
     * @return mixed
     * @throws ViewException
     */
    private function renderFile($view)
    {
        $params = $this->xssFilter($this->data);

        $templateEngine = config()->get('template_engine');

        if ($templateEngine) {
            $engineName = key($templateEngine);
            $engineConfigs = $templateEngine[$engineName];

            return HookManager::call('templateRenderer', [
                        'configs' => $engineConfigs,
                        'view' => $view,
                        'params' => $params
            ]);
        } else {
            return $this->defaultRenderer($view, $params);
        }
    }

    /**
     * Default Renderer
     * @param string $view
     * @param array $params
     * @return string
     * @throws \Error
     */
    private function defaultRenderer($view, $params = [])
    {
        $file = $this->findFile($view);

        try {
            ob_start();
            ob_implicit_flush(0);

            if (!empty($params)) {
                extract($params, EXTR_OVERWRITE);
            }

            require $file;

            return ob_get_clean();
        } catch (Error $e) {
            ob_clean();
            exit($e->getMessage());
        }
    }

    /**
     * XSS Filter
     * @param mixed $data
     * @return mixed
     */
    private function xssFilter($data)
    {
        if (is_string($data)) {
            $this->cleaner($data);
            $data = [$data];
        } else {
            array_walk_recursive($data, [$this, 'cleaner']);
        }

        return $data;
    }

    /**
     * Cleaner
     * @param mixed $value
     */
    private function cleaner(&$value)
    {
        if (is_object($value)) {
            $this->xssFilter($value);
        } else {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }

    /**
     * Render Debug Bar
     * @param string $view
     * @return string
     */
    private function renderDebugBar($view)
    {
        $debugbarRenderer = (new Debugger())->run($view);

        return $debugbarRenderer->renderHead() . $debugbarRenderer->render();
    }

}
