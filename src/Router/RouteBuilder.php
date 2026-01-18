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

namespace Quantum\Router;

use Closure;

/**
 * Class RouteBuilder
 * @package Quantum\Router
 */
class RouteBuilder
{
    /**
     * @param array<string, Closure> $moduleClosures moduleName => closure(Route $collector): void
     * @param array<string, array>   $moduleConfigs  moduleName => config options
     * @return array
     */
    public function build(array $moduleClosures, array $moduleConfigs = []): array
    {
        $allRoutes = [];

        foreach ($moduleClosures as $module => $closure) {
            $options = $moduleConfigs[$module] ?? [];

            $routeCollector = new Route($module, $options);

            $closure($routeCollector);

            foreach ($routeCollector->getRuntimeRoutes() as $runtimeRoute) {
                $allRoutes[] = $runtimeRoute;
            }
        }

        return $allRoutes;
    }
}
