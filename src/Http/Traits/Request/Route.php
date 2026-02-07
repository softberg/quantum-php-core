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
 * @since 3.0.0
 */

namespace Quantum\Http\Traits\Request;

use Quantum\Router\MatchedRoute;

/**
 * Trait Route
 * @package Quantum\Http\Request
 */
trait Route
{
    /**
     * @var MatchedRoute|null
     */
    private static ?MatchedRoute $route = null;

    /**
     * @param MatchedRoute|null $route
     * @return void
     */
    public static function setMatchedRoute(?MatchedRoute $route): void
    {
        self::$route = $route;
    }

    /**
     * @return MatchedRoute|null
     */
    public static function getMatchedRoute(): ?MatchedRoute
    {
        return self::$route;
    }

}
