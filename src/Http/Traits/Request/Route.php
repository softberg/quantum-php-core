<?php

declare(strict_types=1);

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
    private ?MatchedRoute $route = null;

    public function setMatchedRoute(?MatchedRoute $route): void
    {
        $this->route = $route;
    }

    public function getMatchedRoute(): ?MatchedRoute
    {
        return $this->route;
    }

}
