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

use Quantum\Router\Exceptions\RouteException;

/**
 * Class PatternCompiler
 * @internal Compiles and matches route patterns against request URIs.
 * @package Quantum\Router
 */
class PatternCompiler
{
    protected const VALID_PARAM_NAME_PATTERN = '/^[a-zA-Z]+$/';

    protected const PARAM_TYPES = [
        ':alpha' => '[a-zA-Z]',
        ':num' => '[0-9]',
        ':any' => '[^\/]',
    ];

    /**
     * @var array
     */
    protected array $params = [];

    /**
     * Check whether the given URI matches the route pattern and store extracted params.
     * @param Route $route
     * @param string $uri
     * @return bool
     * @throws RouteException
     */
    public function match(Route $route, string $uri): bool
    {
        [$pattern, $segmentParams] = $this->compile($route);

        $requestUri = urldecode(parse_url($uri, PHP_URL_PATH) ?? '');

        if (!preg_match('/^' . $this->escape($pattern) . '$/u', $requestUri, $matches)) {
            $this->params = [];
            return false;
        }

        $this->params = $this->extractParams($segmentParams, $matches);

        return true;
    }

    /**
     * Compile route pattern into regex and param metadata.
     * @param Route $route
     * @return array
     * @throws RouteException
     */
    public function compile(Route $route): array
    {
        $segments = explode('/', trim($route->getPattern(), '/'));

        $pattern = '(\/)?';
        $params = [];

        $lastIndex = (int) array_key_last($segments);

        foreach ($segments as $index => $segment) {
            $segmentParam = $this->getSegmentParam($segment, $index, $lastIndex);

            if ($segmentParam !== []) {
                $this->checkParamName($params, $segmentParam['name']);

                $params[] = $segmentParam;

                $pattern = $this->normalizePattern(
                    $pattern,
                    $segmentParam,
                    $index,
                    $lastIndex
                );
            } else {
                if ($index === $lastIndex) {
                    $pattern .= $segment . '(\/)?';
                } else {
                    $pattern .= $segment . '(\/)';
                }
            }
        }

        return [$pattern, $params];
    }

    /**
     * Return parameters extracted from the last successful match.
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Build the final named parameter map from regex matches and param metadata.
     * @param array $params
     * @param array $matches
     * @return array
     */
    protected function extractParams(array $params, array $matches): array
    {
        $values = array_diff($matches, ['', '/']);
        $result = [];

        foreach ($params as $param) {
            if ($param['name'] !== null) {
                $result[$param['name']] = $values[$param['name']] ?? null;
            }
        }

        return $result;
    }

    /**
     * Detect and build parameter definition for a single route segment if present.
     * @param string $segment
     * @param int $index
     * @param int $lastIndex
     * @return array
     * @throws RouteException
     */
    protected function getSegmentParam(string $segment, int $index, int $lastIndex): array
    {
        foreach (static::PARAM_TYPES as $type => $expr) {
            if (preg_match('/\[(.*=)*(' . $type . ')(:([0-9]+))*\](\?)?/', $segment, $match)) {
                return $this->getParamPattern($match, $expr, $index, $lastIndex);
            }
        }

        return [];
    }

    /**
     * Generate the regex pattern and name for a matched parameter segment.
     * @param array $match
     * @param string $expr
     * @param int $index
     * @param int $lastIndex
     * @return array
     * @throws RouteException
     */
    protected function getParamPattern(array $match, string $expr, int $index, int $lastIndex): array
    {
        $name = $this->getParamName($match, $index);

        $pattern = '(?<' . $name . '>' . $expr;

        if (isset($match[4]) && is_numeric($match[4])) {
            $pattern .= (isset($match[5]) && $match[5] === '?')
                ? '{0,' . $match[4] . '})'
                : '{' . $match[4] . '})';
        } else {
            $pattern .= (isset($match[5]) && $match[5] === '?')
                ? '*)'
                : '+)';
        }

        if (isset($match[5]) && $match[5] === '?') {
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
     * Resolve and validate the parameter name from a segment match.
     * @param array $match
     * @param int $index
     * @return string
     * @throws RouteException
     */
    protected function getParamName(array $match, int $index): string
    {
        $name = $match[1] ? rtrim($match[1], '=') : null;

        if ($name === null) {
            return '_segment' . $index;
        }

        if (!preg_match(static::VALID_PARAM_NAME_PATTERN, $name)) {
            throw RouteException::paramNameNotValid();
        }

        return $name;
    }

    /**
     * Ensure the parameter name is unique within the route pattern.
     * @param array $params
     * @param string $name
     * @return void
     * @throws RouteException
     */
    protected function checkParamName(array $params, string $name): void
    {
        foreach ($params as $param) {
            if ($param['name'] === $name) {
                throw RouteException::paramNameNotAvailable($name);
            }
        }
    }

    /**
     * Adjust the accumulated route regex before appending the last segment pattern.
     * @param string $routePattern
     * @param array $segmentParam
     * @param int $index
     * @param int $lastIndex
     * @return string
     */
    protected function normalizePattern(
        string $routePattern,
        array $segmentParam,
        int $index,
        int $lastIndex
    ): string {
        if ($index === $lastIndex) {
            if (mb_substr($routePattern, -5) === '(\/)?') {
                $routePattern = mb_substr($routePattern, 0, -5);
            } elseif (mb_substr($routePattern, -4) === '(\/)') {
                $routePattern = mb_substr($routePattern, 0, -4);
            }
        }

        return $routePattern . $segmentParam['pattern'];
    }

    /**
     * Escape forward slashes in a regex fragment safely.
     * @param string $str
     * @return string
     */
    protected function escape(string $str): string
    {
        return str_replace('/', '\/', stripslashes($str));
    }
}
