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

namespace Quantum\Factory;

use Quantum\Mvc\QtView;

/**
 * Class ViewFactory
 * @package Quantum\Factory
 */
Class ViewFactory
{

    /**
     * Instance of QtView
     * @var QtView 
     */
    private static $viewInstance = null;

    /**
     * GetInstance
     *
     * @return QtView
     */
    public static function getInstance()
    {
        if (self::$viewInstance === null) {
            self::$viewInstance = new QtView();
        }

        return self::$viewInstance;
    }

    /**
     * __call magic 
     * 
     * @param string $method
     * @param array $args
     */
    public function __call($method, $args = null)
    {
        return self::getInstance()->{$method}(...$args);
    }

}
