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
 * @since 1.0.0
 */

namespace Quantum\Mvc;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Libraries\Debugger\Debugger;
use Quantum\Exceptions\ViewException;
use Quantum\Libraries\Config\Config;
use Quantum\Factory\ViewFactory;
use Quantum\Hooks\HookManager;
use Quantum\Helpers\Helper;

/**
 * Base View Class
 *
 * Qt_View class is a base class that responsible for rendering and
 * outputting the view
 *
 * @package Quantum
 * @category MVC
 */
class Qt_View
{

    /**
     * Layout file
     *
     * @var string
     */
    private static $layout;

    /**
     * View file
     *
     * @var string
     */
    public static $view;

    /**
     * The data shared between layout and view
     *
     * @var array
     */
    public static $sharedData = [];

    /**
     * Qt_View constructor.
     *
     * @throws ViewException
     */
    public function __construct()
    {
        if (get_caller_class() != ViewFactory::class) {
            throw new ViewException(Helper::_message(ExceptionMessages::DIRECT_VIEW_INCTANCE, [ViewFactory::class]));
        }
    }

    /**
     * Sets a layout
     *
     * @param string $layout
     * @return void
     */
    public function setLayout($layout)
    {
        self::$layout = $layout;
    }

    /**
     * Share
     *
     * Set the shared data, which becomes available in layout and view
     *
     * @param mixed $data
     * @return void
     */
    public function share($data)
    {
        foreach ($data as $key => $value) {
            Qt_View::$sharedData[$key] = $value;
        }
    }

    /**
     * Render
     *
     * Renders the view
     *
     * @param string $view
     * @param array $params
     * @throws ViewException
     */
    public function render($view, $params = [])
    {
        self::$view = self::renderFile($view, $params);

        if (!empty(self::$layout)) {
            echo self::renderFile(self::$layout);

            if (filter_var(get_config('debug'), FILTER_VALIDATE_BOOLEAN)) {
                $this->renderDebugBar($view);
            }
        }
    }

    /**
     * Output
     *
     * Outputs the view
     *
     * @param string $view
     * @param array $params
     * @throws ViewException
     */
    public function output($view, $params = [])
    {
        echo self::renderFile($view, $params);
    }

    /**
     * Find File
     *
     * Finds a given file
     *
     * @param $file
     * @return string
     * @throws ViewException
     */
    private function findFile($file)
    {
        $filePath = modules_dir() . DS . current_module() . DS . 'Views' . DS . $file . '.php';
        if (!file_exists($filePath)) {
            $filePath = base_dir() . DS . 'base' . DS . 'views' . DS . $file . '.php';
            if (!file_exists($filePath)) {
                throw new ViewException(Helper::_message(ExceptionMessages::VIEW_FILE_NOT_FOUND, $file));
            }
        }

        return $filePath;
    }

    /**
     * Render File
     *
     * Renders the view
     *
     * @param string $view
     * @param array $parmas
     * @return object|string
     * @throws ViewException
     */
    private function renderFile($view, $params = [])
    {
        $viewData = self::xssFilter($params);
        $sharedData = self::xssFilter(self::$sharedData);

        $params = array_merge($viewData, $sharedData);

        $templateEngine = Config::get('template_engine');

        if ($templateEngine) {
            $engineName = key($templateEngine);
            $engineConfigs = $templateEngine[$engineName];

            return HookManager::call('templateRenderer', [
                        'configs' => $engineConfigs,
                        'view' => $view,
                        'params' => $params
            ]);
        } else {
            return self::defaultRenderer($view, $params);
        }
    }

    /**
     * Default Renderer
     *
     * Renders html view
     * 
     * @param string $view
     * @param array $params
     * @return string
     * @throws ViewException
     */
    private static function defaultRenderer($view, $params = [])
    {
        $file = self::findFile($view);

        ob_start();
        ob_implicit_flush(false);

        if ($params) {
            extract($params, EXTR_OVERWRITE);
        }

        require $file;

        return ob_get_clean();
    }

    /**
     * XSS Filter
     *
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
     * 
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
     *
     * @param string $view
     */
    private function renderDebugBar($view)
    {
        $debugbarRenderer = Debugger::runDebuger($view);
        echo $debugbarRenderer->renderHead();
        echo $debugbarRenderer->render();
    }

}
