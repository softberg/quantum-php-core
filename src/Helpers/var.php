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
 * @since 2.9.5
 */

use Symfony\Component\VarExporter\Exception\ExceptionInterface;
use Symfony\Component\VarExporter\VarExporter;
use Quantum\Libraries\Config\ConfigException;
use Quantum\Exceptions\DiException;
use Quantum\Logger\LoggerException;
use Quantum\Logger\LoggerManager;

/**
 * Reports error
 * @param $var
 * @param array $context
 * @return void
 * @throws ConfigException
 * @throws DiException
 * @throws LoggerException
 * @throws ReflectionException
 */
function error($var, array $context = [])
{
    LoggerManager::getHandler()->error($var, $context);
}

/**
 * Reports warning
 * @param $var
 * @param array $context
 * @return void
 * @throws ConfigException
 * @throws DiException
 * @throws LoggerException
 * @throws ReflectionException
 */
function warning($var, array $context = [])
{
    LoggerManager::getHandler()->warning($var, $context);
}

/**
 * Reports notice
 * @param $var
 * @param array $context
 * @return void
 * @throws ConfigException
 * @throws DiException
 * @throws LoggerException
 * @throws ReflectionException
 */
function notice($var, array $context = [])
{
    LoggerManager::getHandler()->notice($var, $context);
}

/**
 * Reports info
 * @param $var
 * @param array $context
 * @return void
 * @throws ConfigException
 * @throws DiException
 * @throws LoggerException
 * @throws ReflectionException
 */
function info($var, array $context = [])
{
    LoggerManager::getHandler()->info($var, $context);
}

/**
 * Reports debug
 * @param $var
 * @param array $context
 * @return void
 * @throws ConfigException
 * @throws DiException
 * @throws LoggerException
 * @throws ReflectionException
 */
function debug($var, array $context = [])
{
    LoggerManager::getHandler()->debug($var, $context);
}

/**
 * Exports the variable
 * @param mixed $var
 * @return string
 * @throws ExceptionInterface
 */
function export($var): string
{
    return VarExporter::export($var);
}
