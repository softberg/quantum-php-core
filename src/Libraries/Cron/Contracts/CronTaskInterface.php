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

namespace Quantum\Libraries\Cron\Contracts;

/**
 * Interface CronTaskInterface
 * @package Quantum\Libraries\Cron
 */
interface CronTaskInterface
{
    /**
     * Get the cron expression
     * @return string
     */
    public function getExpression(): string;

    /**
     * Get the task name
     * @return string
     */
    public function getName(): string;

    /**
     * Check if the task should run at the current time
     * @return bool
     */
    public function shouldRun(): bool;

    /**
     * Execute the task
     * @return void
     */
    public function handle(): void;
}
