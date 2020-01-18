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
     * @var AuthenticableInterface
     */
    private static $viewInstance = null;

    /**
     * GetInstance
     *
     * @return Qt_View
     */
    public static function getInstance()
    {
        if (self::$viewInstance === null) {
            self::$viewInstance = new Qt_View();
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
        self::getInstance()->{$method}(...$args);
    }

}
