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
use Quantum\Libraries\Config\Config;
use Quantum\Hooks\HookManager;

/**
 * Base View Class
 * 
 * Qt_View class is a base class that responsible for rendering and 
 * outputting the view
 * 
 * @package Quantum
 * @subpackage MVC
 * @category MVC
 */
class Qt_View {

    /**
     * Current route
     * 
     * @var mixed 
     */
    private static $currentRoute;

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
     * Class constructor 
     * 
     * @param mixed $currentRoute
     * @return void
     */
    public function __construct($currentRoute) {
        self::$currentRoute = $currentRoute;
    }

    /**
     * Sets a layout
     * 
     * @param string $layout
     * @return void
     */
    public function setLayout($layout) {
        self::$layout = $layout;
    }

    /**
     * Renders a layout
     * 
     * @param array $sharedData
     * @return string
     */
    public function renderLayout($sharedData = array()) {
        return self::renderFile(self::$layout, array(), $sharedData);
    }

    /**
     * Render
     * 
     * Renders a view
     * 
     * @param string $view
     * @param array $params
     * @param bool $output
     * @param array $sharedData
     * @uses self::renderFile
     * @return void
     */
    public function render($view, $params = array(), $output = false, $sharedData = array()) {
        self::$view = self::renderFile($view, $params, $sharedData);

        if ($output) {
            echo self::$view;
        }

        if (!empty(self::$layout)) {
            echo self::renderLayout($sharedData);

            if (isset($sharedData['debugbarRenderer']) && !is_null($sharedData['debugbarRenderer'])) {
                $debugbarRenderer = $sharedData['debugbarRenderer'];
                echo $debugbarRenderer->renderHead();
                echo $debugbarRenderer->render();
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
     * @param array $sharedData
     * @uses self::renderFile
     * @return void
     */
    public function output($view, $params = array(), $sharedData = array()) {
        echo self::renderFile($view, $params, $sharedData);
    }

    /**
     * Find File
     * 
     * Finds a given file
     * 
     * @param string $file
     * @return string
     * @throws \Exception When file is not found
     */
    private function findFile($file) {
        $filePath = MODULES_DIR . DS . self::$currentRoute['module'] . DS . 'Views' . DS . $file . '.php';
        if (!file_exists($filePath)) {
            $filePath = BASE_DIR . DS . 'base' . DS . 'views' . DS . $file . '.php';
            if (!file_exists($filePath)) {
                throw new \Exception(_message(ExceptionMessages::VIEW_FILE_NOT_FOUND, $file));
            }
        }

        return $filePath;
    }

    /**
     * Render File
     * 
     * Renders a view  
     * 
     * @param string $view
     * @param array $parmas
     * @param array $sharedData
     * @return string
     */
    private function renderFile($view, $parmas = array(), $sharedData = array()) {
        $templateEngine = Config::get('template_engine');

        if ($templateEngine) {
            $engineName = key($templateEngine);
            $engineConfigs = $templateEngine[$engineName];

            return HookManager::call('templateRenderer', [
                        'configs' => $engineConfigs,
                        'currentModule' => self::$currentRoute['module'],
                        'view' => $view,
                        'params' => $parmas,
                        'sharedData' => $sharedData
            ]);
        } else {
            return self::defaultRenderer($view, $parmas, $sharedData);
        }
    }

    /**
     * Default Renderer
     * 
     * Renders html view  
     * 
     * @param string $view
     * @param array $parmas
     * @param array $sharedData
     * @return string
     */
    private static function defaultRenderer($view, $parmas = array(), $sharedData = array()) {
        $file = self::findFile($view);

        ob_start();
        ob_implicit_flush(false);

        if ($parmas) {
            extract($parmas, EXTR_OVERWRITE);
        }

        if ($sharedData) {
            extract($sharedData, EXTR_OVERWRITE);
        }

        require $file;

        return ob_get_clean();
    }

}
