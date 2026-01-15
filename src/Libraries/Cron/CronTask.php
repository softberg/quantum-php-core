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

namespace Quantum\Libraries\Cron;

use Quantum\Libraries\Cron\Contracts\CronTaskInterface;
use Quantum\Libraries\Cron\Exceptions\CronException;
use Cron\CronExpression;

/**
 * Class CronTask
 * @package Quantum\Libraries\Cron
 */
class CronTask implements CronTaskInterface
{
    /**
     * Cron expression instance
     * @var CronExpression
     */
    private $cronExpression;

    /**
     * Task name
     * @var string
     */
    private $name;

    /**
     * Task callback
     * @var callable
     */
    private $callback;

    /**
     * CronTask constructor
     * @param string $name
     * @param string $expression
     * @param callable $callback
     * @throws CronException
     */
    public function __construct(string $name, string $expression, callable $callback)
    {
        $this->name = $name;
        $this->callback = $callback;

        try {
            $this->cronExpression = new CronExpression($expression);
        } catch (\Exception $e) {
            throw CronException::invalidExpression($expression);
        }
    }

    /**
     * @inheritDoc
     */
    public function getExpression(): string
    {
        return $this->cronExpression->getExpression();
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function shouldRun(): bool
    {
        return $this->cronExpression->isDue();
    }

    /**
     * @inheritDoc
     */
    public function handle(): void
    {
        call_user_func($this->callback);
    }

    /**
     * Get the next run date
     * @return \DateTime
     */
    public function getNextRunDate(): \DateTime
    {
        return $this->cronExpression->getNextRunDate();
    }

    /**
     * Get the previous run date
     * @return \DateTime
     */
    public function getPreviousRunDate(): \DateTime
    {
        return $this->cronExpression->getPreviousRunDate();
    }
}
