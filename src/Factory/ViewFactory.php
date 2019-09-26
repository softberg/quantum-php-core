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
 * @since 1.6.0
 */

namespace Quantum\Factory;

use Quantum\Mvc\Qt_View;

/**
 * Class ViewFactory
 * @package Quantum\Factory
 */
Class ViewFactory
{

    /**
     * Get View
     *
     * @return object
     * @throws \Exception
     */
    public function view()
    {
        return new Qt_View();
    }

    /**
     * Render
     *
     * Renders the view
     *
     * @param string $viewFile
     * @param array $params
     * @param bool $output
     * @throws \Exception
     * @return void
     */
    public function render($viewFile, $params = [], $output = false)
    {
        $this->view()->render($viewFile, $params, $output, Qt_View::$sharedData);
    }

    /**
     * Sets a layout
     *
     * @param $layout
     * @throws \Exception
     * @return void
     */
    public function setLayout($layout)
    {
        $this->view()->setLayout($layout);
    }

    /**
     * Output
     *
     * Outputs the view
     *
     * @param string $view
     * @param array $params
     */
    public function output($view, $params = [])
    {
        $this->view()->output($view, $params, Qt_View::$sharedData);
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

}
