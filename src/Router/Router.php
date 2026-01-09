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

use Quantum\App\Exceptions\StopExecutionException;
use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use DebugBar\DebugBarException;
use Quantum\Debugger\Debugger;
use Quantum\Http\Request;
use ReflectionException;

/**
 * Class Router
 * @package Quantum\Router
 */
class Router extends RouteController
{
    public const VALID_PARAM_NAME_PATTERN = '/^[a-zA-Z]+$/';

    /**
     * Parameter types
     */
    public const PARAM_TYPES = [
        ':alpha' => '[a-zA-Z]',
        ':num' => '[0-9]',
        ':any' => '[^\/]',
    ];

    /**
     * Request instance
     * @var Request
     */
    private $request;

    /**
     * matched routes
     * @var array
     */
    private $matchedRoutes = [];

    /**
     * Router constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Finds the current route
     * @throws BaseException
     * @throws ConfigException
     * @throws DebugBarException
     * @throws DiException
     * @throws ReflectionException
     * @throws RouteException
     * @throws StopExecutionException
     */
    public function findRoute()
    {
        $uri = $this->request->getUri();

        if (!$uri) {
            throw RouteException::routeNotFound();
        }

        $this->matchedRoutes = $this->findMatches($uri);

        if ($this->matchedRoutes === []) {
            stop(function () {
                page_not_found();
            });
        }

        if (count($this->matchedRoutes) > 1) {
            $this->checkCollision();
        }

        $currentRoute = $this->currentRoute();

        if (!$currentRoute) {
            throw RouteException::incorrectMethod($this->request->getMethod());
        }

        $this->handleCaching($currentRoute);

        self::setCurrentRoute($currentRoute);

        info($this->collectDebugData($currentRoute), ['tab' => Debugger::ROUTES]);
    }

    /**
     * Resets the routes
     */
    public function resetRoutes()
    {
        parent::$currentRoute = [];
        $this->matchedRoutes = [];
    }

    /**
     * @param array $route
     * @return void
     */
    private function handleCaching(array $route): void
    {
        $viewCache = ViewCache::getInstance();

        $defaultCaching = $viewCache->isEnabled();

        $shouldCacheForRoute = $route['cache_settings']['shouldCache'] ?? $defaultCaching;

        $viewCache->enableCaching($shouldCacheForRoute);

        if ($shouldCacheForRoute && !empty($route['cache_settings']['ttl'])) {
            $viewCache->setTtl($route['cache_settings']['ttl']);
        }
    }

    /**
     * Gets the current route
     * @return array|null
     */
    private function currentRoute(): ?array
    {
        foreach ($this->matchedRoutes as $matchedRoute) {
            if ($this->checkMethod($matchedRoute)) {
                return $matchedRoute;
            }
        }

        return null;
    }

    /**
     * Collects debug data
     * @param array $currentRoute
     * @return array
     */
    private function collectDebugData(array $currentRoute): array
    {
        $routeInfo = [];

        foreach ($currentRoute as $key => $value) {
            $routeInfo[ucfirst($key)] = json_encode($value);
        }

        return $routeInfo;
    }

    /**
     * Finds matches by pattern
     * @param string $uri
     * @return array
     * @throws RouteException
     */
    private function findMatches(string $uri): array
    {
        $requestUri = urldecode(parse_url($uri, PHP_URL_PATH));

        $matches = [];

        foreach (self::$routes as $route) {
            [$pattern, $params] = $this->handleRoutePattern($route);

            if (preg_match('/^' . $this->escape($pattern) . '$/u', $requestUri, $matchedParams)) {
                $route['uri'] = $uri;
                $route['params'] = $this->routeParams($params, $matchedParams);
                $route['pattern'] = $pattern;
                $matches[] = $route;
            }
        }

        return $matches;
    }

    /**
     * Handles the route pattern
     * @param array $route
     * @return array
     * @throws RouteException
     */
    private function handleRoutePattern(array $route): array
    {
        $routeSegments = explode('/', trim($route['route'], '/'));

        $routePattern = '(\/)?';
        $routeParams = [];

        $lastIndex = (int)array_key_last($routeSegments);

        foreach ($routeSegments as $index => $segment) {
            $segmentParam = $this->getSegmentParam($segment, $index, $lastIndex);

            if ($segmentParam !== []) {
                $this->checkParamName($routeParams, $segmentParam['name']);

                $routeParams[] = [
                    'route_pattern' => $segment,
                    'pattern' => $segmentParam['pattern'],
                    'name' => $segmentParam['name'],
                ];

                $routePattern = $this->normalizePattern($routePattern, $segmentParam, $index, $lastIndex);
            } else {
                $routePattern .= $segment . ($index !== $lastIndex ? '(\/)' : '');
            }
        }

        return [
            $routePattern,
            $routeParams,
        ];
    }

    /**
     * Normalize the pattern
     * @param string $routePattern
     * @param array $segmentParam
     * @param int $index
     * @param int $lastIndex
     * @return string
     */
    private function normalizePattern(string $routePattern, array $segmentParam, int $index, int $lastIndex): string
    {
        if ($index === $lastIndex) {
            if (mb_substr($routePattern, -5) === '(\/)?') {
                $routePattern = mb_substr($routePattern, 0, mb_strlen($routePattern) - 5);
            } elseif (mb_substr($routePattern, -4) === '(\/)') {
                $routePattern = mb_substr($routePattern, 0, mb_strlen($routePattern) - 4);
            }
        }

        return $routePattern . $segmentParam['pattern'];
    }

    /**
     * Gets the route parameters
     * @param array $params
     * @param array $arguments
     * @return array
     */
    private function routeParams(array $params, array $arguments): array
    {
        $arguments = array_diff($arguments, ['', '/']);

        foreach ($params as &$param) {
            $param['value'] = $arguments[$param['name']] ?? null;
            if (mb_substr($param['name'], 0, 1) === '_') {
                $param['name'] = null;
            }
        }

        return $params;
    }

    /**
     * Checks the segment for parameter
     * @param string $segment
     * @param int $index
     * @param int $lastIndex
     * @return array
     * @throws RouteException
     */
    private function getSegmentParam(string $segment, int $index, int $lastIndex): array
    {
        foreach (self::PARAM_TYPES as $type => $expr) {
            if (preg_match('/\[(.*=)*(' . $type . ')(:([0-9]+))*\](\?)?/', $segment, $match)) {
                return $this->getParamPattern($match, $expr, $index, $lastIndex);
            }
        }

        return [];
    }

    /**
     * Checks the parameter name availability
     * @param array $routeParams
     * @param string $name
     * @throws RouteException
     */
    private function checkParamName(array $routeParams, string $name)
    {
        foreach ($routeParams as $param) {
            if ($param['name'] == $name) {
                throw RouteException::paramNameNotAvailable($name);
            }
        }
    }

    /**
     * Finds pattern for parameter
     * @param array $match
     * @param string $expr
     * @param int $index
     * @param int $lastIndex
     * @return array
     * @throws RouteException
     */
    private function getParamPattern(array $match, string $expr, int $index, int $lastIndex): array
    {
        $name = $this->getParamName($match, $index);

        $pattern = '(?<' . $name . '>' . $expr;

        if (isset($match[4]) && is_numeric($match[4])) {
            $pattern .= (isset($match[5]) && $match[5] == '?') ? '{0,' . $match[4] . '})' : '{' . $match[4] . '})';
        } else {
            $pattern .= (isset($match[5]) && $match[5] == '?') ? '*)' : '+)';
        }

        if (isset($match[5]) && $match[5] == '?') {
            $pattern = ($index === $lastIndex ? '(\/)?' . $pattern : $pattern . '(\/)?');
        } else {
            $pattern = ($index === $lastIndex ? '(\/)' . $pattern : $pattern . '(\/)');
        }

        return [
            'name' => $name,
            'pattern' => $pattern,
        ];
    }

    /**
     * Gets the parameter name
     * @param array $match
     * @param int $index
     * @return string
     * @throws RouteException
     */
    private function getParamName(array $match, int $index): string
    {
        $name = $match[1] ? rtrim($match[1], '=') : null;

        if ($name === null) {
            return '_segment' . $index;
        }

        if (!preg_match(self::VALID_PARAM_NAME_PATTERN, $name)) {
            throw RouteException::paramNameNotValid();
        }

        return $name;
    }

    /**
     * Checks the route collisions
     * @throws RouteException
     */
    private function checkCollision()
    {
        $length = count($this->matchedRoutes);

        for ($i = 0; $i < $length - 1; $i++) {
            for ($j = $i + 1; $j < $length; $j++) {
                if ($this->matchedRoutes[$i]['method'] == $this->matchedRoutes[$j]['method']) {
                    throw RouteException::repetitiveRouteSameMethod($this->matchedRoutes[$j]['method']);
                }
                if ($this->matchedRoutes[$i]['module'] != $this->matchedRoutes[$j]['module']) {
                    throw RouteException::repetitiveRouteDifferentModules();
                }
            }
        }
    }

    /**
     * Checks the request method against defined route method
     * @param array $matchedRoute
     * @return bool
     */
    private function checkMethod(array $matchedRoute): bool
    {
        $allowedMethods = explode('|', $matchedRoute['method']);

        return in_array($this->request->getMethod(), $allowedMethods, true);
    }

    /**
     * Escapes the slashes
     * @param string $str
     * @return string
     */
    private function escape(string $str): string
    {
        return str_replace('/', '\/', stripslashes($str));
    }
}
