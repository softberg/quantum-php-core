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

namespace Quantum\Routes;

/**
 * RouterController Class
 * 
 * Base abstract class
 * 
 * @package Quantum
 * @category Routes
 */
abstract class RouteController
{

    /**
     * Contains current route information
     * @var array 
     */
    protected static $currentRoute = null;

    /**
     * Gets the current route
     * @return array
     */
    public static function getCurrentRoute()
    {
        return self::$currentRoute;
    }

}
