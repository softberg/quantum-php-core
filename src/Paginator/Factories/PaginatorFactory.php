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
 * @since 2.9.8
 */

namespace Quantum\Paginator\Factories;

use Quantum\Paginator\Exceptions\PaginatorException;
use Quantum\Paginator\Adapters\ModelPaginator;
use Quantum\Paginator\Adapters\ArrayPaginator;
use Quantum\App\Exceptions\BaseException;
use Quantum\Paginator\Paginator;

/**
 * Class PaginatorFactory
 * @package Quantum\Paginator
 */
class PaginatorFactory
{

    /**
     * Supported adapters
     */
    const ADAPTERS = [
        Paginator::ARRAY => ArrayPaginator::class,
        Paginator::MODEL => ModelPaginator::class,
    ];

    /**
     * Required parameters for each adapter type.
     */
    const REQUIRED_PARAMS = [
        Paginator::ARRAY => ['items'],
        Paginator::MODEL => ['model'],
    ];

    /**
     * Default parameters applied to all adapters.
     */
    const DEFAULT_PARAMS = [
        'perPage' => 10,
        'page' => 1,
    ];

    /**
     * Creates a new paginator instance using the selected adapter type.
     * @param string $type
     * @param array $params
     * @return Paginator
     * @throws BaseException
     * @throws PaginatorException
     */
    public static function create(string $type, array $params): Paginator
    {
        if (!isset(self::ADAPTERS[$type])) {
            throw PaginatorException::adapterNotSupported($type);
        }

        self::validateRequiredParams($type, $params);

        $adapterClass = self::ADAPTERS[$type];

        $constructorArgs = self::buildConstructorArgs($type, $params);

        $adapter = new $adapterClass(...$constructorArgs);

        return new Paginator($adapter);
    }

    /**
     * Validates that all required parameters are present.
     * @param string $type
     * @param array $params
     * @return void
     * @throws PaginatorException
     */
    private static function validateRequiredParams(string $type, array $params): void
    {
        $missing = array_diff(self::REQUIRED_PARAMS[$type], array_keys($params));

        if (!empty($missing)) {
            throw PaginatorException::missingRequiredParams($type, $missing);
        }
    }

    /**
     * Builds the list of arguments in the correct order for adapter instantiation.
     * @param string $type
     * @param array $params
     * @return array
     */
    private static function buildConstructorArgs(string $type, array $params): array
    {
        $params = array_merge(self::DEFAULT_PARAMS, $params);

        $args = [];

        foreach (self::REQUIRED_PARAMS[$type] as $key) {
            $args[] = $params[$key];
        }

        foreach (array_keys(self::DEFAULT_PARAMS) as $key) {
            $args[] = $params[$key];
        }

        return $args;
    }
}