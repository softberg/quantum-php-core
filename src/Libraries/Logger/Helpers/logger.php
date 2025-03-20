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
 * @since 2.9.6
 */

use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Logger\Factories\LoggerFactory;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Libraries\Logger\Logger;

/**
 * @param string|null $adapter
 * @return Logger
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
 * @param $var
 * @param array $context
 * @return void
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 * @throws BaseException
 */
function error($var, array $context = [])
{
    LoggerFactory::get()->error($var, $context);
}

/**
 * Reports warning
 * @param $var
 * @param array $context
 * @return void
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function warning($var, array $context = [])
{
    LoggerFactory::get()->warning($var, $context);
}

/**
 * Reports notice
 * @param $var
 * @param array $context
 * @return void
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function notice($var, array $context = [])
{
    LoggerFactory::get()->notice($var, $context);
}

/**
 * Reports info
 * @param $var
 * @param array $context
 * @return void
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function info($var, array $context = [])
{
    LoggerFactory::get()->info($var, $context);
}

/**
 * Reports debug
 * @param $var
 * @param array $context
 * @return void
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function debug($var, array $context = [])
{
    LoggerFactory::get()->debug($var, $context);
}