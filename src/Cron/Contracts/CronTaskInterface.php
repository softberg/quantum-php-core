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

namespace Quantum\Cron\Contracts;

/**
 * Interface CronTaskInterface
 * @package Quantum\Cron
 */
interface CronTaskInterface
{
    /**
     * Get the cron expression
     */
    public function getExpression(): string;

    /**
     * Get the task name
     */
    public function getName(): string;

    /**
     * Check if the task should run at the current time
     */
    public function shouldRun(): bool;

    /**
     * Execute the task
     */
    public function handle(): void;
}
