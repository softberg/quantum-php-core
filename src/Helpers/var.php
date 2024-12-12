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
    $logger = LoggerManager::getHandler();

    if (is_debug_mode() || LoggerConfig::getLogLevel(LogLevel::ERROR) >= LoggerConfig::getAppLogLevel()) {
        $logger->error($var);
    }
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
    $logger = LoggerManager::getHandler();

    if (is_debug_mode() || LoggerConfig::getLogLevel(LogLevel::WARNING) >= LoggerConfig::getAppLogLevel()) {
        $logger->warning($var);
    }
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
    $logger = LoggerManager::getHandler();

    if (is_debug_mode() || LoggerConfig::getLogLevel(LogLevel::NOTICE) >= LoggerConfig::getAppLogLevel()) {
        $logger->notice($var);
    }
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
    $logger = LoggerManager::getHandler();

    if (is_debug_mode() || LoggerConfig::getLogLevel(LogLevel::INFO) >= LoggerConfig::getAppLogLevel()) {
        $logger->info($var);
    }
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
    $logger = LoggerManager::getHandler();

    if (is_debug_mode() || LoggerConfig::getLogLevel(LogLevel::DEBUG) >= LoggerConfig::getAppLogLevel()) {
        $logger->debug($var);
    }
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
