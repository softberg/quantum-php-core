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
 * @since 1.0.0
 */

namespace Quantum\Routes;

use Quantum\Exceptions\RouteException;
use Quantum\Exceptions\ExceptionMessages;
use Quantum\Hooks\HookManager;
use Quantum\Hooks\HookController;

/**
 * Router Class
 *
 * Router class parses URIS and determine routing
 *
 * @package Quantum
 * @subpackage Routes
 * @category Routes
 */
class Router extends RouteController
{

    /**
     * List of routes
     *
     * @var array
     */
    public $routes = [];

    /**
     * @var array
     */
    private $matchedUris = [];

    /**
     * @var array
     */
    private $routesGroups = [];

    /**
     * @var string
     */
    private $requestUri = '';

    /**
     * Find Route
     *
     * Matches any routes that may exists in config/routes.php file of specific module
     * against the URI to determine current route and current module
     *
     * @return void
     * @throws RouteException When repetitive route was found
     */
    public function findRoute()
    {
        if (isset($_SERVER['REQUEST_URI'])) {

            $this->findStraightMatches();

            if (!$this->matchedUris) {
                $this->findPatternMatches();
            }

            if (!$this->matchedUris) {
                self::$currentRoute = null;
                HookManager::call('pageNotFound');
            }

            if ($this->matchedUris) {
                if (count($this->routesGroups)) {

                    $this->routesGroups[0]['uri'] = $this->requestUri ?? '';

                    self::$currentModule = $this->routesGroups[0]['module'];

                    for ($i = 0; $i < count($this->routesGroups) - 1; $i++) {
                        for ($j = $i + 1; $j < count($this->routesGroups); $j++) {
                            if ($this->routesGroups[$i]['method'] == $this->routesGroups[$j]['method']) {
                                self::$currentRoute = null;
                                throw new RouteException(_message(ExceptionMessages::REPETITIVE_ROUTE_SAME_METHOD, $this->routesGroups[$j]['method']));
                                break 2;
                            }
                            if ($this->routesGroups[$i]['module'] != $this->routesGroups[$j]['module']) {
                                self::$currentRoute = null;
                                throw new RouteException(ExceptionMessages::REPETITIVE_ROUTE_DIFFERENT_MODULES);
                                break 2;
                            }
                        }
                    }

                    foreach ($this->routesGroups as $route) {
                        if (strpos($route['method'], '|') !== false) {
                            if (in_array($_SERVER['REQUEST_METHOD'], explode('|', $route['method']))) {
                                self::$currentRoute = $route;
                                break;
                            }
                        } else if ($_SERVER['REQUEST_METHOD'] == $route['method']) {
                            self::$currentRoute = $route;
                            break;
                        }
                    }
                } else {
                    self::$currentModule = $this->routesGroups[0]['module'];
                    self::$currentRoute = $this->routesGroups[0];
                }
            }

            HookManager::call('handleHeaders');

            if ($_SERVER['REQUEST_METHOD'] != 'OPTIONS') {
                $this->checkMethod();
            }
        }
    }

    /**
     * Finds straight matches
     */
    private function findStraightMatches()
    {
        $this->requestUri = trim(urldecode(preg_replace('/[?]/', '', $_SERVER['REQUEST_URI'])), '/');

        foreach ($this->routes as $route) {
            if ($this->requestUri == trim($route['route'], '/')) {
                $route['args'] = [];
                $this->matchedUris[] = $route['route'];
                $this->routesGroups[] = $route;
            }
        }
    }

    /**
     * Finds matches by pattern
     */
    private function findPatternMatches()
    {
        $this->requestUri = urldecode(parse_url($_SERVER['REQUEST_URI'])['path']);

        foreach ($this->routes as $route) {
            $pattern = trim($route['route'], '/');
            $pattern = str_replace('/', '\/', $pattern);
            $pattern = preg_replace_callback('/(\\\\\/)*\[(:num)(:([0-9]+))*\](\?)?/', array($this, 'getPattern'), $pattern);
            $pattern = preg_replace_callback('/(\\\\\/)*\[(:alpha)(:([0-9]+))*\](\?)?/', array($this, 'getPattern'), $pattern);
            $pattern = preg_replace_callback('/(\\\\\/)*\[(:any)(:([0-9]+))*\](\?)?/', array($this, 'getPattern'), $pattern);

            $pattern = mb_substr($pattern, 0, 4) != '(\/)' ? '(\/)?' . $pattern : $pattern;

            preg_match("/^" . $pattern . "$/u", $this->requestUri, $matches);

            if (count($matches)) {
                $this->matchedUris = $matches[0] ?: '/';
                array_shift($matches);
                $route['args'] = array_diff($matches, ['', '/']);
                $route['pattern'] = $pattern;
                $this->routesGroups[] = $route;
            }
        }
    }

    /**
     * Matches the http method defined in config/routes.php file of specific module
     * against request method to determine current route
     *
     * @return void
     * @throws RouteException When Http method is other the defined in config/routes.php of sepcific module
     */
    private function checkMethod()
    {
        if (strpos(self::$currentRoute['method'], '|') !== false) {
            if (!in_array($_SERVER['REQUEST_METHOD'], explode('|', self::$currentRoute['method']))) {
                throw new RouteException(_message(ExceptionMessages::INCORRECT_METHOD, $_SERVER['REQUEST_METHOD']));
            }
        } else if ($_SERVER['REQUEST_METHOD'] != self::$currentRoute['method']) {
            throw new RouteException(_message(ExceptionMessages::INCORRECT_METHOD, $_SERVER['REQUEST_METHOD']));
        }
    }

    /**
     * Finds url pattern
     *
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
