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
 * @since 2.5.0
 */

use Symfony\Component\VarExporter\VarExporter;
use Quantum\Logger\MessageLogger;
use Quantum\Logger\Logger;

/**
 * Reports error
 * @param mixed $var
 */
function error($var)
{
    $logger = new Logger(new MessageLogger);
    $logger->error($var);
}

/**
 * Reports warning
 * @param mixed $var
 */
function warning($var)
{
    $logger = new Logger(new MessageLogger);
    $logger->warning($var);
}

/**
 * Reports notice
 * @param mixed $var
 */
function notice($var)
{
    $logger = new Logger(new MessageLogger);
    $logger->notice($var);
}

/**
 * Reports info
 * @param mixed $var
 */
function info($var)
{
    $logger = new Logger(new MessageLogger);
    $logger->info($var);
}

/**
 * Reports debug
 * @param mixed $var
 */
function debug($var)
{
    $logger = new Logger(new MessageLogger);
    $logger->debug($var);
}

/**
 * Exports the variable
 * @param mixed $var
 * @return string
 * @throws \Symfony\Component\VarExporter\Exception\ExceptionInterface
 */
function export($var): string
{
    return VarExporter::export($var);
}

