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
use Quantum\Libraries\Config\Config;
use Quantum\Factory\ViewFactory;
use Quantum\Hooks\HookManager;

/**
 * Base View Class
 *
 * Qt_View class is a base class that responsible for rendering and
 * outputting the view
 *
 * @package Quantum\Mvc
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
     * @throws \Exception
     */
    public function __construct()
    {
        if (get_caller_class() != ViewFactory::class) {
            throw new \Exception(_message(ExceptionMessages::DIRECT_VIEW_INCTANCE, [ViewFactory::class]));
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
     * Renders the layout
     *
     * @param array $sharedData
     * @return string
     */
    public function renderLayout($sharedData = [])
    {
        return self::renderFile(self::$layout, [], $sharedData);
    }

    /**
     * Render
     *
     * Renders the view
     *
     * @param $view
     * @param array $params
     * @param bool $output
     * @param array $sharedData
     * @throws \Exception
     */
    public function render($view, $params = [], $output = false, $sharedData = [])
    {
        self::$view = self::renderFile($view, $params, $sharedData);

        if ($output) {
            echo self::$view;
        }

        if (!empty(self::$layout)) {
            echo self::renderLayout($sharedData);

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
     * @param $view
     * @param array $params
     * @param array $sharedData
     * @throws \Exception
     */
    public function output($view, $params = [], $sharedData = [])
    {
        echo self::renderFile($view, $params, $sharedData);
    }

    /**
     * Find File
     *
     * Finds a given file
     *
     * @param $file
     * @return string
     * @throws \Exception
     */
    private function findFile($file)
    {
        $filePath = modules_dir() . DS . current_module() . DS . 'Views' . DS . $file . '.php';
        if (!file_exists($filePath)) {
            $filePath = base_dir() . DS . 'base' . DS . 'views' . DS . $file . '.php';
            if (!file_exists($filePath)) {
                throw new \Exception(_message(ExceptionMessages::VIEW_FILE_NOT_FOUND, $file));
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
     * @param array $sharedData
     * @return object|string
     * @throws \Exception
     */
    private function renderFile($view, $params = [], $sharedData = [])
    {
        $params = self::xssFilter($params);
        $sharedData = self::xssFilter($sharedData);

        $templateEngine = Config::get('template_engine');

        if ($templateEngine) {
            $engineName = key($templateEngine);
            $engineConfigs = $templateEngine[$engineName];

            return HookManager::call('templateRenderer', [
                'configs' => $engineConfigs,
                'view' => $view,
                'params' => $params,
                'sharedData' => $sharedData
            ]);
        } else {
            return self::defaultRenderer($view, $params, $sharedData);
        }
    }

    /**
     * Default Renderer
     *
     * Renders html view
     * @param string $view
     * @param array $params
     * @param array $sharedData
     * @return string
     * @throws \Exception
     */
    private static function defaultRenderer($view, $params = [], $sharedData = [])
    {
        $file = self::findFile($view);

        ob_start();
        ob_implicit_flush(false);

        if ($params) {
            extract($params, EXTR_OVERWRITE);
        }

        if ($sharedData) {
            extract($sharedData, EXTR_OVERWRITE);
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
        array_walk_recursive($data, function (&$value) {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        });

        return $data;
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
