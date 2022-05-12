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

namespace Quantum\Factory;

use Quantum\Mvc\QtView;

/**
 * Class ViewFactory
 * @package Quantum\Factory
 * @mixin QtView
 */
class ViewFactory
{

    /**
     * Instance of QtView
     * @var QtView|null
     */
    private static $viewInstance = null;

    /**
     * QtView instance
     * @return \Quantum\Mvc\QtView|null
     */
    public static function getInstance(): ?QtView
    {
        if (self::$viewInstance === null) {
            self::$viewInstance = new QtView();
        }

        return self::$viewInstance;
    }

    /**
     * Allows to call methods of QtView class 
     * @param string $method
     * @param array|null $args
     * @return mixed
     */
    public function __call(string $method, ?array $args = null)
    {
        return self::getInstance()->{$method}(...$args);
    }

}
