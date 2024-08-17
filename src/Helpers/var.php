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
 * @since 2.9.0
 */

use Symfony\Component\VarExporter\Exception\ExceptionInterface;
use Symfony\Component\VarExporter\VarExporter;
use Quantum\Contracts\ReportableInterface;
use Quantum\Logger\MessageLogger;
use Quantum\Logger\Logger;

/**
 * Reports error
 * @param mixed $var
 * @param ReportableInterface|null $loggerAdapter
 */
function error($var, ?ReportableInterface $loggerAdapter = null)
{
    $logger = new Logger($loggerAdapter ?: new MessageLogger);
    $logger->error($var);
}

/**
 * Reports warning
 * @param mixed $var
 * @param ReportableInterface|null $loggerAdapter
 */
function warning($var, ?ReportableInterface $loggerAdapter = null)
{
    $logger = new Logger($loggerAdapter ?: new MessageLogger);
    $logger->warning($var);
}

/**
 * Reports notice
 * @param mixed $var
 * @param ReportableInterface|null $loggerAdapter
 */
function notice($var, ?ReportableInterface $loggerAdapter = null)
{
    $logger = new Logger($loggerAdapter ?: new MessageLogger);
    $logger->notice($var);
}

/**
 * Reports info
 * @param mixed $var
 * @param ReportableInterface|null $loggerAdapter
 */
function info($var, ?ReportableInterface $loggerAdapter = null)
{
    $logger = new Logger($loggerAdapter ?: new MessageLogger);
    $logger->info($var);
}

/**
 * Reports debug
 * @param mixed $var
 * @param ReportableInterface|null $loggerAdapter
 */
function debug($var, ?ReportableInterface $loggerAdapter = null)
{
    $logger = new Logger($loggerAdapter ?: new MessageLogger);
    $logger->debug($var);
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
