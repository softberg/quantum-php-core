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

use Quantum\Config\Exceptions\ConfigException;
use Quantum\Logger\Factories\LoggerFactory;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Logger\Logger;

/**
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function logger(?string $adapter = null): Logger
{
    return LoggerFactory::get($adapter);
}

/**
 * Reports error
 * @param string $var
 * @param array<string, mixed> $context
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function error(string $var, array $context = []): void
{
    LoggerFactory::get()->error($var, $context);
}

/**
 * Reports warning
 * @param string $var
 * @param array<string, mixed> $context
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function warning(string $var, array $context = []): void
{
    LoggerFactory::get()->warning($var, $context);
}

/**
 * Reports notice
 * @param string $var
 * @param array<string, mixed> $context
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function notice(string $var, array $context = []): void
{
    LoggerFactory::get()->notice($var, $context);
}

/**
 * Reports info
 * @param string $var
 * @param array<string, mixed> $context
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function info(string $var, array $context = []): void
{
    LoggerFactory::get()->info($var, $context);
}

/**
 * Reports debug
 * @param string $var
 * @param array<string, mixed> $context
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function debug(string $var, array $context = []): void
{
    LoggerFactory::get()->debug($var, $context);
}
