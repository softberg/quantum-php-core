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
 * @since 2.8.0
 */

namespace Quantum\Router;

use Quantum\Exceptions\RouteException;
use Quantum\Debugger\Debugger;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Psr\Log\LogLevel;

/**
 * Class Router
 * @package Quantum\Router
 */
class Router extends RouteController
{

    /**
     * Parameter types
     */
    const PARAM_TYPES = [
        'alpha',
        'num',
        'any'
    ];

    /**
     * Request instance
     * @var \Quantum\Http\Request;
     */
    private $request;

    /**
     * Response instance
     * @var \Quantum\Http\Response;
     */
    private $response;

    /**
     * List of routes
     * @var array
     */
    private $routes = [];

    /**
     * matched routes
     * @var array
     */
    private $matchedRoutes = [];

    /**
     * Matched URI
     * @var string
     */
    private $matchedUri = null;

    /**
     * Router constructor.
     * @param \Quantum\Http\Request $request
     * @param \Quantum\Http\Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Finds the current route
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\RouteException
     * @throws \Quantum\Exceptions\StopExecutionException
     * @throws \ReflectionException
     */
    public function findRoute()
    {
        $this->resetRoutes();

        $uri = $this->request->getUri();

        if (!$uri) {
            throw RouteException::notFound();
        }

        $this->findPatternMatches($uri);

        if (!count($this->matchedRoutes)) {
            stop(function () {
                $this->response->html(partial('errors/404'), 404);
            });
        }

        if (count($this->matchedRoutes) > 1) {
            $this->checkCollision();
        }

        $matchedRoute = current($this->matchedRoutes);

        if ($this->request->getMethod() != 'OPTIONS') {
            $this->checkMethod($matchedRoute);
        }

        $matchedRoute['uri'] = $uri;

        self::setCurrentRoute($matchedRoute);

        if (filter_var(config()->get(Debugger::DEBUG_ENABLED), FILTER_VALIDATE_BOOLEAN)) {
            $routeInfo = [];

            array_walk($matchedRoute, function ($value, $key) use (&$routeInfo) {
                $routeInfo[ucfirst($key)] = json_encode($value); //is_array($value) ? implode(', ', $value) : $value;
            });

            Debugger::addToStore(Debugger::ROUTES, LogLevel::INFO, $routeInfo);
        }
    }

    /**
     * Set Routes
     * @param array $routes
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Get Routes
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Resets the routes
     */
    private function resetRoutes()
    {
        parent::$currentRoute = null;
        $this->matchedUri = null;
        $this->matchedRoutes = [];
    }

    /**
     * Finds matches by pattern
     * @param string $uri
     */
    private function findPatternMatches(string $uri)
    {
        $requestUri = urldecode(parse_url($uri)['path']);

        foreach ($this->routes as $route) {

            list($pattern, $params) = $this->handleRoutePattern($route);

            preg_match("/^" . $this->escape($pattern) . "$/u", $requestUri, $matches);

            if (count($matches)) {
                $this->matchedUri = array_shift($matches) ?: '/';

                $route['params'] = $this->routeParams($params, $matches);
                $route['pattern'] = $pattern;
                $this->matchedRoutes[] = $route;
            }
        }
    }

    private function handleRoutePattern(array $route): array
    {
        $routeSegments = explode('/', trim($route['route'], '/'));

        $routePattern = '(\/)?';
        $routeParams = [];

        $lastIndex = array_key_last($routeSegments);

        foreach ($routeSegments as $index => $segment) {
            $segmentParam = $this->checkSegment($segment, $index, $lastIndex);

            if ($segmentParam) {
                if ($segmentParam['name']) {
                    $this->checkParamName($routeParams, $segmentParam['name']);
                }

                $routeParams[] = [
                    'route_pattern' => $segment,
                    'pattern' => $segmentParam['pattern'],
                    'name' => $segmentParam['name']
                ];

                $routePattern = $this->normilizePattern($routePattern, $segmentParam, $index, $lastIndex);
            } else {
                $routePattern .= $segment;

                if ($index != $lastIndex) {
                    $routePattern .= '(\/)';
                }
            }
        }

        return [
            $routePattern,
            $routeParams
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
    private function normilizePattern(string $routePattern, array $segmentParam, int $index, int $lastIndex): string
    {
        if ($index == $lastIndex) {
            if (mb_substr($routePattern, -5) == '(\/)?') {
                $routePattern = mb_substr($routePattern, 0, mb_strlen($routePattern) - 5);
            } elseif (mb_substr($routePattern, -4) == '(\/)') {
                $routePattern = mb_substr($routePattern, 0, mb_strlen($routePattern) - 4);
            }
        }

        return $routePattern .= $segmentParam['pattern'];
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
            if (mb_substr($param['name'], 0, 1) == '_') {
                $param['name'] = null;
            }
        }

        return $params;
    }

    /**
     * Checks the segment for parameter
     * @param string $segment
     * @return array|boolean
     */
    private function checkSegment(string $segment, int $index, int $lastIndex)
    {
        foreach (self::PARAM_TYPES as $type) {
            if (preg_match('/\[(.*=)*(:' . $type . ')(:([0-9]+))*\](\?)?/', $segment, $match)) {
                return $this->getParamPattern($match, $index, $lastIndex);
            }
        }

        return false;
    }

    /**
     * Checks the parameter name availability
     * @param array $routeParams
     * @param string $name
     * @throws \Quantum\Exceptions\RouteException
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
     * @return array
     */
    private function getParamPattern(array $match, int $index, int $lastIndex): array
    {
        $name = $match[1] ? rtrim($match[1], '=') : null;

        if ($name) {
            if (!preg_match('/^[a-zA-Z]+$/', $name)) {
                throw RouteException::paramNameNotValid($name);
            }
        } else {
            $name = '_segment' . $index;
        }

        $pattern = '';

        switch ($match[2]) {
            case ':num':
                $pattern .= '(?<' . $name . '>[0-9]';
                break;
            case ':alpha':
                $pattern .= '(?<' . $name . '>[a-zA-Z]';
                break;
            case ':any':
                $pattern .= '(?<' . $name . '>[^\/]';
                break;
        }

        if (isset($match[4]) && is_numeric($match[4])) {
            if (isset($match[5]) && $match[5] == '?') {
                $pattern .= '{0,' . $match[4] . '})';
            } else {
                $pattern .= '{' . $match[4] . '})';
            }
        } else {
            if (isset($match[5]) && $match[5] == '?') {
                $pattern .= '*)';
            } else {
                $pattern .= '+)';
            }
        }

        if (isset($match[5]) && $match[5] == '?') {
            $pattern = ($index == $lastIndex ? '(\/)?' . $pattern : $pattern . '(\/)?');
        } else {
            $pattern = ($index == $lastIndex ? '(\/)' . $pattern : $pattern . '(\/)');
        }


        return [
            'name' => $name,
            'pattern' => $pattern
        ];
    }

    /**
     * Checks the route collisions
     * @throws \Quantum\Exceptions\RouteException
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
     * @throws \Quantum\Exceptions\RouteException
     */
    private function checkMethod(array $matchedRoute)
    {
        if (strpos($matchedRoute['method'], '|') !== false) {
            if (!in_array($this->request->getMethod(), explode('|', $matchedRoute['method']))) {
                throw RouteException::incorrectMethod($this->request->getMethod());
            }
        } else if ($this->request->getMethod() != $matchedRoute['method']) {
            throw RouteException::incorrectMethod($this->request->getMethod());
        }
    }

    /**
     * Escapes the slashes
     * @param string $str
     * @return string
     */
    private function escape($str)
    {
        return str_replace('/', '\/', stripslashes($str));
    }

}
