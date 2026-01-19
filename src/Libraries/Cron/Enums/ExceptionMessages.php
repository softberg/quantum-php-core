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

namespace Quantum\Libraries\Cron\Enums;

/**
 * Enum ExceptionMessages
 * @package Quantum\Libraries\Cron
 */
final class ExceptionMessages
{
    public const TASK_NOT_FOUND = 'Cron task "%s" not found';
    public const INVALID_EXPRESSION = 'Invalid cron expression: %s';
    public const LOCK_ACQUIRE_FAILED = 'Failed to acquire lock for task "%s"';
    public const TASK_EXECUTION_FAILED = 'Task "%s" execution failed: %s';
    public const INVALID_TASK_FILE = 'Invalid task file "%s": must return array or CronTask instance';
    public const CRON_DIRECTORY_NOT_FOUND = 'Cron directory not found: %s';
    public const LOCK_DIRECTORY_NOT_WRITABLE = 'Lock directory is not writable: %s';
}
