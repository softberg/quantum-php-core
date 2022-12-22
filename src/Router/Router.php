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
 * @since 2.9.0
 */

namespace Quantum\Router;

use Quantum\Exceptions\StopExecutionException;
use Quantum\Exceptions\RouteException;
use Quantum\Exceptions\ViewException;
use Quantum\Exceptions\DiException;
use Quantum\Debugger\Debugger;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Error\LoaderError;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;
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
        ':alpha' => '[a-zA-Z]',
        ':num' => '[0-9]',
        ':any' => '[^\/]'
    ];

    /**
     * Request instance
     * @var Request;
     */
    private $request;

    /**
     * Response instance
     * @var Response;
     */
    private $response;

    /**
     * List of routes
     * @var array
     */
    private static $routes = [];

    /**
     * matched routes
     * @var array
     */
    private $matchedRoutes = [];

    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Finds the current route
     * @throws StopExecutionException
     * @throws ViewException
     * @throws DiException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws RouteException
     * @throws ReflectionException
     */
    public function findRoute()
    {
        $uri = $this->request->getUri();

        if (!$uri) {
            throw RouteException::notFound();
        }

        $this->findPatternMatches($uri);

        if (!count($this->matchedRoutes)) {
            stop(function () {
                $this->handleNotFound();
            });
        }

        if (count($this->matchedRoutes) > 1) {
            $this->checkCollision();
        }

        $matchedRoute = current($this->matchedRoutes);

        $this->checkMethod($matchedRoute);

        $matchedRoute['uri'] = $uri;

        self::setCurrentRoute($matchedRoute);

        if (filter_var(config()->get(Debugger::DEBUG_ENABLED), FILTER_VALIDATE_BOOLEAN)) {
            $routeInfo = [];

            array_walk($matchedRoute, function ($value, $key) use (&$routeInfo) {
                $routeInfo[ucfirst($key)] = json_encode($value);
            });

            Debugger::addToStore(Debugger::ROUTES, LogLevel::INFO, $routeInfo);
        }
    }

    /**
     * Set Routes
     * @param array $routes
     */
    public static function setRoutes(array $routes)
    {
        self::$routes = $routes;
    }

    /**
     * Get Routes
     * @return array
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * Resets the routes
     */
    public function resetRoutes()
    {
        parent::$currentRoute = null;
        $this->matchedRoutes = [];
    }

    /**
     * Finds matches by pattern
     * @param string $uri
     * @throws RouteException
     */
    private function findPatternMatches(string $uri)
    {
        $requestUri = urldecode(parse_url($uri)['path']);

        foreach (self::$routes as $route) {

            list($pattern, $params) = $this->handleRoutePattern($route);

            preg_match("/^" . $this->escape($pattern) . "$/u", $requestUri, $matches);

            if (count($matches)) {
                $route['params'] = $this->routeParams($params, $matches);
                $route['pattern'] = $pattern;
                $this->matchedRoutes[] = $route;
            }
        }
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
            $segmentParam = $this->checkSegment($segment, $index, $lastIndex);

            if (!empty($segmentParam)) {
                $this->checkParamName($routeParams, $segmentParam['name']);

                $routeParams[] = [
                    'route_pattern' => $segment,
                    'pattern' => $segmentParam['pattern'],
                    'name' => $segmentParam['name']
                ];

                $routePattern = $this->normalizePattern($routePattern, $segmentParam, $index, $lastIndex);
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
    private function normalizePattern(string $routePattern, array $segmentParam, int $index, int $lastIndex): string
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
     * @param int $index
     * @param int $lastIndex
     * @return array
     * @throws RouteException
     */
    private function checkSegment(string $segment, int $index, int $lastIndex): array
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
        $pattern = '';

        $name = $this->getParamName($match, $index);

        $pattern .= '(?<' . $name . '>' . $expr;

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
     * Gets the parameter name
     * @param array $match
     * @param int $index
     * @return string
     * @throws RouteException
     */
    private function getParamName(array $match, int $index): string
    {
        $name = $match[1] ? rtrim($match[1], '=') : null;

        if ($name) {
            if (!preg_match('/^[a-zA-Z]+$/', $name)) {
                throw RouteException::paramNameNotValid();
            }
        } else {
            $name = '_segment' . $index;
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
     * @throws RouteException
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
    private function escape(string $str): string
    {
        return str_replace('/', '\/', stripslashes($str));
    }

    /**
     * Handles page not found
     * @throws DiException
     * @throws ViewException
     * @throws ReflectionException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function handleNotFound()
    {
        if ($this->request->getHeader('Accept') == 'application/json') {
            $this->response->json(['status' => 'error', 'message' => 'Page not found'], 404);
        } else {
            $this->response->html(partial('errors/404'), 404);
        }
    }

}
