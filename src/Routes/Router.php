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
 * @since 2.7.0
 */

namespace Quantum\Routes;

use Quantum\Exceptions\RouteException;
use Quantum\Debugger\Debugger;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Psr\Log\LogLevel;

/**
 * Class Router
 * @package Quantum\Routes
 */
class Router extends RouteController
{

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

        $this->findStraightMatches($uri);

        if (!count($this->matchedRoutes)) {
            $this->findPatternMatches($uri);
        }

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

        $matchedRoute['uri'] = $this->request->getUri();

        self::setCurrentRoute($matchedRoute);

        if (filter_var(config()->get('debug'), FILTER_VALIDATE_BOOLEAN)) {
            $routeInfo = [];

            array_walk($matchedRoute, function ($value, $key) use (&$routeInfo) {
                $routeInfo[ucfirst($key)] = is_array($value) ? implode(', ', $value) : $value;
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
     * Finds straight matches
     * @param string $uri
     */
    private function findStraightMatches(string $uri)
    {
        $requestUri = trim(urldecode(preg_replace('/[?]/', '', $uri)), '/');

        foreach ($this->routes as $route) {
            if ($requestUri == trim($route['route'], '/')) {
                $route['args'] = [];
                $this->matchedUri = $route['route'];
                $this->matchedRoutes[] = $route;
            }
        }
    }

    /**
     * Finds matches by pattern
     * @param string $uri
     */
    private function findPatternMatches(string $uri)
    {
        $requestUri = urldecode(parse_url($uri)['path']);

        foreach ($this->routes as $route) {
            $pattern = trim($route['route'], '/');
            $pattern = str_replace('/', '\/', $pattern);
            $pattern = preg_replace_callback('/(\\\\\/)*\[(:num)(:([0-9]+))*\](\?)?/', [$this, 'getPattern'], $pattern);
            $pattern = preg_replace_callback('/(\\\\\/)*\[(:alpha)(:([0-9]+))*\](\?)?/', [$this, 'getPattern'], $pattern);
            $pattern = preg_replace_callback('/(\\\\\/)*\[(:any)(:([0-9]+))*\](\?)?/', [$this, 'getPattern'], $pattern);

            $pattern = mb_substr($pattern, 0, 4) != '(\/)' ? '(\/)?' . $pattern : $pattern;

            preg_match("/^" . $pattern . "$/u", $requestUri, $matches);

            if (count($matches)) {
                $this->matchedUri = reset($matches) ?: '/';
                array_shift($matches);
                $route['args'] = array_diff($matches, ['', '/']);
                $route['pattern'] = $pattern;
                $this->matchedRoutes[] = $route;
            }
        }
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
     * Finds URL pattern
     * @param array $matches
     * @return string
     */
    private function getPattern(array $matches): string
    {
        $replacement = '';

        if (isset($matches[5]) && $matches[5] == '?') {
            $replacement .= '(\/)?';
        } else {
            $replacement .= '(\/)';
        }

        switch ($matches[2]) {
            case ':num':
                $replacement .= '([0-9]';
                break;
            case ':alpha':
                $replacement .= '([a-zA-Z]';
                break;
            case ':any':
                $replacement .= '([^\/]';
                break;
        }

        if (isset($matches[4]) && is_numeric($matches[4])) {
            if (isset($matches[5]) && $matches[5] == '?') {
                $replacement .= '{0,' . $matches[4] . '})';
            } else {
                $replacement .= '{' . $matches[4] . '})';
            }
        } else {
            if (isset($matches[5]) && $matches[5] == '?') {
                $replacement .= '*)';
            } else {
                $replacement .= '+)';
            }
        }

        return $replacement;
    }

}
