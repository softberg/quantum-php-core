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

namespace Quantum\Libraries\Cron\Exceptions;

use Quantum\Libraries\Cron\Enums\ExceptionMessages;

/**
 * Class CronException
 * @package Quantum\Libraries\Cron
 */
class CronException extends \Exception
{
    /**
     * Task not found exception
     * @param string $taskName
     * @return CronException
     */
    public static function taskNotFound(string $taskName): CronException
    {
        return new self(sprintf(ExceptionMessages::TASK_NOT_FOUND->value, $taskName));
    }

    /**
     * Invalid cron expression exception
     * @param string $expression
     * @return CronException
     */
    public static function invalidExpression(string $expression): CronException
    {
        return new self(sprintf(ExceptionMessages::INVALID_EXPRESSION->value, $expression));
    }

    /**
     * Lock acquire failed exception
     * @param string $taskName
     * @return CronException
     */
    public static function lockAcquireFailed(string $taskName): CronException
    {
        return new self(sprintf(ExceptionMessages::LOCK_ACQUIRE_FAILED->value, $taskName));
    }

    /**
     * Task execution failed exception
     * @param string $taskName
     * @param string $error
     * @return CronException
     */
    public static function taskExecutionFailed(string $taskName, string $error): CronException
    {
        return new self(sprintf(ExceptionMessages::TASK_EXECUTION_FAILED->value, $taskName, $error));
    }

    /**
     * Invalid task file exception
     * @param string $file
     * @return CronException
     */
    public static function invalidTaskFile(string $file): CronException
    {
        return new self(sprintf(ExceptionMessages::INVALID_TASK_FILE->value, $file));
    }

    /**
     * Cron directory not found exception
     * @param string $directory
     * @return CronException
     */
    public static function cronDirectoryNotFound(string $directory): CronException
    {
        return new self(sprintf(ExceptionMessages::CRON_DIRECTORY_NOT_FOUND->value, $directory));
    }

    /**
     * Lock directory not writable exception
     * @param string $directory
     * @return CronException
     */
    public static function lockDirectoryNotWritable(string $directory): CronException
    {
        return new self(sprintf(ExceptionMessages::LOCK_DIRECTORY_NOT_WRITABLE->value, $directory));
    }
}
