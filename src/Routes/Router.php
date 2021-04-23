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

use Quantum\Exceptions\RouteException;
use Quantum\Hooks\HookManager;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Router Class
 *
 * Router class parses URIS and determine routing
 *
 * @package Quantum
 * @category Routes
 */
class Router extends RouteController
{

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
     * @param Request $request
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Finds the route defined in config/routes.php file of specific module
     * against the URI to determine current route and current module
     * @throws RouteException
     */
    public function findRoute()
    {
        $this->resetRoutes();

        $uri = $this->request->getUri();

        $this->findStraightMatches($uri);

        if (!count($this->matchedRoutes)) {
            $this->findPatternMatches($uri);
        }

        if (!count($this->matchedRoutes)) {
            HookManager::call('pageNotFound', $this->response);
        }

        if (count($this->matchedRoutes) > 1) {
            $this->checkCollision();
        }

        $matchedRoute = current($this->matchedRoutes);

        if ($this->request->getMethod() != 'OPTIONS') {
            $this->checkMethod($matchedRoute);
        }

        $matchedRoute['uri'] = $this->request->getUri();

        parent::$currentRoute = $matchedRoute;
    }

    /**
     * Set Routes
     * @param array $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }

    /**
     * Get Routes
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    private function resetRoutes()
    {
        parent::$currentRoute = null;
        $this->matchedUri = null;
        $this->matchedRoutes = [];
    }

    /**
     * Finds straight matches
     * @return void
     */
    private function findStraightMatches($uri)
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
     * @return void
     */
    private function findPatternMatches($uri)
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
     * @throws RouteException
     */
    private function checkCollision()
    {
        $length = count($this->matchedRoutes);

        for ($i = 0; $i < $length - 1; $i++) {
            for ($j = $i + 1; $j < $length; $j++) {
                if ($this->matchedRoutes[$i]['method'] == $this->matchedRoutes[$j]['method']) {
                    throw new RouteException(_message(RouteException::REPETITIVE_ROUTE_SAME_METHOD, $this->matchedRoutes[$j]['method']));
                }
                if ($this->matchedRoutes[$i]['module'] != $this->matchedRoutes[$j]['module']) {
                    throw new RouteException(RouteException::REPETITIVE_ROUTE_DIFFERENT_MODULES);
                }
            }
        }
    }

    /**
     * Checks the request method against defined route method
     * @param array $matchedRoute
     * @throws RouteException
     */
    private function checkMethod($matchedRoute)
    {
        if (strpos($matchedRoute['method'], '|') !== false) {
            if (!in_array($this->request->getMethod(), explode('|', $matchedRoute['method']))) {
                throw new RouteException(_message(RouteException::INCORRECT_METHOD, $this->request->getMethod()));
            }
        } else if ($this->request->getMethod() != $matchedRoute['method']) {
            throw new RouteException(_message(RouteException::INCORRECT_METHOD, $this->request->getMethod()));
        }
    }

    /**
     * Finds URL pattern
     * @param string $matches
     * @return string
     */
    private function getPattern($matches)
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
