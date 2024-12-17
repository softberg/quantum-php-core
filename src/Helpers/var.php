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
use Quantum\Exceptions\ConfigException;
use Quantum\Exceptions\LangException;
use Quantum\Exceptions\DiException;
use Quantum\Logger\LoggerException;
use Quantum\Logger\LoggerManager;
use Quantum\Logger\LoggerConfig;
use Psr\Log\LogLevel;

/**
 * Reports error
 * @param mixed $var
 * @return void
 * @throws ReflectionException
 * @throws ConfigException
 * @throws DiException
 * @throws LangException
 * @throws LoggerException
 */
function error($var)
{
    LoggerManager::getHandler()->error($var);
}

/**
 * Reports warning
 * @param mixed $var
 * @return void
 * @throws ConfigException
 * @throws DiException
 * @throws LangException
 * @throws LoggerException
 * @throws ReflectionException
 */
function warning($var)
{
    LoggerManager::getHandler()->warning($var);
}

/**
 * Reports notice
 * @param mixed $var
 * @return void
 * @throws ConfigException
 * @throws DiException
 * @throws LangException
 * @throws LoggerException
 * @throws ReflectionException
 */
function notice($var)
{
    LoggerManager::getHandler()->notice($var);
}

/**
 * Reports info
 * @param mixed $var
 * @return void
 * @throws ConfigException
 * @throws DiException
 * @throws LangException
 * @throws LoggerException
 * @throws ReflectionException
 */
function info($var)
{
    LoggerManager::getHandler()->info($var);
}

/**
 * Reports debug
 * @param mixed $var
 * @return void
 * @throws ConfigException
 * @throws DiException
 * @throws LangException
 * @throws LoggerException
 * @throws ReflectionException
 */
function debug($var)
{
    LoggerManager::getHandler()->debug($var);
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
