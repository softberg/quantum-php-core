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

use Quantum\Libraries\Cron\CronManager;
use Quantum\Libraries\Cron\CronTask;
use Quantum\Libraries\Cron\Schedule;

if (!function_exists('cron_manager')) {
    /**
     * Get CronManager instance
     * @param string|null $cronDirectory
     * @return CronManager
     */
    function cron_manager(?string $cronDirectory = null): CronManager
    {
        return new CronManager($cronDirectory);
    }
}

if (!function_exists('cron_task')) {
    /**
     * Create a new cron task
     * @param string $name
     * @param string $expression
     * @param callable $callback
     * @return CronTask
     * @throws \Quantum\Libraries\Cron\Exceptions\CronException
     */
    function cron_task(string $name, string $expression, callable $callback): CronTask
    {
        return new CronTask($name, $expression, $callback);
    }
}

if (!function_exists('schedule')) {
    /**
     * Create a new schedule with fluent API
     * @param string $name
     * @return Schedule
     */
    function schedule(string $name): Schedule
    {
        return new Schedule($name);
    }
}
