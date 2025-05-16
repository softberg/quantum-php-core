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
 * @since 2.9.7
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
     * Required parameters for each adapter
     */
    const REQUIRED_PARAMS = [
        Paginator::ARRAY => ['items'],
        Paginator::MODEL => ['orm', 'model'],
    ];

    /**
     * Default parameters
     */
    const DEFAULT_PARAMS = [
        'perPage' => 10,
        'page' => 1,
    ];

    /**
     * @var array
     */
    private static $instances = [];

    /**
     * Get paginator instance
     * @param string $type
     * @param array $params
     * @return Paginator
     * @throws BaseException
     * @throws PaginatorException
     */
    public static function get(string $type, array $params): Paginator
    {
        if (!isset(self::$instances[$type])) {
            self::$instances[$type] = self::createInstance($type, $params);
        }

        return self::$instances[$type];
    }

    /**
     * Create paginator instance
     * @param string $type
     * @param array $params
     * @return Paginator
     * @throws BaseException
     * @throws PaginatorException
     */
    private static function createInstance(string $type, array $params): Paginator
    {
        if (!isset(self::ADAPTERS[$type])) {
            throw PaginatorException::adapterNotSupported($type);
        }

        self::validateRequiredParams($type, $params);

        $params = array_merge(self::DEFAULT_PARAMS, $params);

        $adapterClass = self::ADAPTERS[$type];

        return new Paginator($adapterClass::fromArray($params));
    }

    /**
     * Validate required parameters
     * @param string $type
     * @param array $params
     * @throws PaginatorException
     */
    private static function validateRequiredParams(string $type, array $params): void
    {
        $missingParams = array_diff(self::REQUIRED_PARAMS[$type], array_keys($params));

        if (!empty($missingParams)) {
            throw PaginatorException::missingRequiredParams($type, $missingParams);
        }
    }
}