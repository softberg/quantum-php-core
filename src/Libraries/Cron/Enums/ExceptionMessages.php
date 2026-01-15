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
enum ExceptionMessages: string
{
    case TASK_NOT_FOUND = 'Cron task "%s" not found';
    case INVALID_EXPRESSION = 'Invalid cron expression: %s';
    case LOCK_ACQUIRE_FAILED = 'Failed to acquire lock for task "%s"';
    case TASK_EXECUTION_FAILED = 'Task "%s" execution failed: %s';
    case INVALID_TASK_FILE = 'Invalid task file "%s": must return array or CronTask instance';
    case CRON_DIRECTORY_NOT_FOUND = 'Cron directory not found: %s';
    case LOCK_DIRECTORY_NOT_WRITABLE = 'Lock directory is not writable: %s';
}
