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

use Quantum\Libraries\Cron\Exceptions\CronException;

/**
 * Class Schedule
 * Fluent API for creating cron schedules
 * @package Quantum\Libraries\Cron
 */
class Schedule
{
    /**
     * Task name
     * @var string
     */
    private $name;

    /**
     * Cron expression
     * @var string
     */
    private $expression;

    /**
     * Task callback
     * @var callable|null
     */
    private $callback = null;

    /**
     * Schedule constructor
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Run the task every minute
     * @return self
     */
    public function everyMinute(): self
    {
        $this->expression = '* * * * *';
        return $this;
    }

    /**
     * Run the task every five minutes
     * @return self
     */
    public function everyFiveMinutes(): self
    {
        $this->expression = '*/5 * * * *';
        return $this;
    }

    /**
     * Run the task every ten minutes
     * @return self
     */
    public function everyTenMinutes(): self
    {
        $this->expression = '*/10 * * * *';
        return $this;
    }

    /**
     * Run the task every fifteen minutes
     * @return self
     */
    public function everyFifteenMinutes(): self
    {
        $this->expression = '*/15 * * * *';
        return $this;
    }

    /**
     * Run the task every thirty minutes
     * @return self
     */
    public function everyThirtyMinutes(): self
    {
        $this->expression = '*/30 * * * *';
        return $this;
    }

    /**
     * Run the task hourly
     * @return self
     */
    public function hourly(): self
    {
        $this->expression = '0 * * * *';
        return $this;
    }

    /**
     * Run the task hourly at a specific minute
     * @param int $minute
     * @return self
     */
    public function hourlyAt(int $minute): self
    {
        $this->expression = "{$minute} * * * *";
        return $this;
    }

    /**
     * Run the task every two hours
     * @return self
     */
    public function everyTwoHours(): self
    {
        $this->expression = '0 */2 * * *';
        return $this;
    }

    /**
     * Run the task every three hours
     * @return self
     */
    public function everyThreeHours(): self
    {
        $this->expression = '0 */3 * * *';
        return $this;
    }

    /**
     * Run the task every four hours
     * @return self
     */
    public function everyFourHours(): self
    {
        $this->expression = '0 */4 * * *';
        return $this;
    }

    /**
     * Run the task every six hours
     * @return self
     */
    public function everySixHours(): self
    {
        $this->expression = '0 */6 * * *';
        return $this;
    }

    /**
     * Run the task daily
     * @return self
     */
    public function daily(): self
    {
        $this->expression = '0 0 * * *';
        return $this;
    }

    /**
     * Run the task daily at a specific time
     * @param string $time Format: "HH:MM"
     * @return self
     */
    public function dailyAt(string $time): self
    {
        [$hour, $minute] = explode(':', $time);
        $this->expression = "{$minute} {$hour} * * *";
        return $this;
    }

    /**
     * Run the task twice daily
     * @param int $firstHour
     * @param int $secondHour
     * @return self
     */
    public function twiceDaily(int $firstHour = 1, int $secondHour = 13): self
    {
        $this->expression = "0 {$firstHour},{$secondHour} * * *";
        return $this;
    }

    /**
     * Run the task weekly
     * @return self
     */
    public function weekly(): self
    {
        $this->expression = '0 0 * * 0';
        return $this;
    }

    /**
     * Run the task weekly on a specific day and time
     * @param int $dayOfWeek 0-6 (Sunday = 0)
     * @param string $time Format: "HH:MM"
     * @return self
     */
    public function weeklyOn(int $dayOfWeek, string $time = '0:00'): self
    {
        [$hour, $minute] = explode(':', $time);
        $this->expression = "{$minute} {$hour} * * {$dayOfWeek}";
        return $this;
    }

    /**
     * Run the task monthly
     * @return self
     */
    public function monthly(): self
    {
        $this->expression = '0 0 1 * *';
        return $this;
    }

    /**
     * Run the task monthly on a specific day and time
     * @param int $dayOfMonth
     * @param string $time Format: "HH:MM"
     * @return self
     */
    public function monthlyOn(int $dayOfMonth = 1, string $time = '0:00'): self
    {
        [$hour, $minute] = explode(':', $time);
        $this->expression = "{$minute} {$hour} {$dayOfMonth} * *";
        return $this;
    }

    /**
     * Run the task twice monthly
     * @param int $firstDay
     * @param int $secondDay
     * @param string $time
     * @return self
     */
    public function twiceMonthly(int $firstDay = 1, int $secondDay = 16, string $time = '0:00'): self
    {
        [$hour, $minute] = explode(':', $time);
        $this->expression = "{$minute} {$hour} {$firstDay},{$secondDay} * *";
        return $this;
    }

    /**
     * Run the task quarterly
     * @return self
     */
    public function quarterly(): self
    {
        $this->expression = '0 0 1 1-12/3 *';
        return $this;
    }

    /**
     * Run the task yearly
     * @return self
     */
    public function yearly(): self
    {
        $this->expression = '0 0 1 1 *';
        return $this;
    }

    /**
     * Run the task on weekdays
     * @return self
     */
    public function weekdays(): self
    {
        $this->expression = '0 0 * * 1-5';
        return $this;
    }

    /**
     * Run the task on weekends
     * @return self
     */
    public function weekends(): self
    {
        $this->expression = '0 0 * * 0,6';
        return $this;
    }

    /**
     * Run the task on Mondays
     * @return self
     */
    public function mondays(): self
    {
        return $this->days(1);
    }

    /**
     * Run the task on Tuesdays
     * @return self
     */
    public function tuesdays(): self
    {
        return $this->days(2);
    }

    /**
     * Run the task on Wednesdays
     * @return self
     */
    public function wednesdays(): self
    {
        return $this->days(3);
    }

    /**
     * Run the task on Thursdays
     * @return self
     */
    public function thursdays(): self
    {
        return $this->days(4);
    }

    /**
     * Run the task on Fridays
     * @return self
     */
    public function fridays(): self
    {
        return $this->days(5);
    }

    /**
     * Run the task on Saturdays
     * @return self
     */
    public function saturdays(): self
    {
        return $this->days(6);
    }

    /**
     * Run the task on Sundays
     * @return self
     */
    public function sundays(): self
    {
        return $this->days(0);
    }

    /**
     * Run the task on specific days
     * @param int|array $days
     * @return self
     */
    public function days($days): self
    {
        $days = is_array($days) ? implode(',', $days) : $days;
        $this->expression = "0 0 * * {$days}";
        return $this;
    }

    /**
     * Set the time for the task
     * @param string $time Format: "HH:MM"
     * @return self
     */
    public function at(string $time): self
    {
        [$hour, $minute] = explode(':', $time);

        // Replace hour and minute in existing expression
        $parts = explode(' ', $this->expression);
        $parts[0] = $minute;
        $parts[1] = $hour;
        $this->expression = implode(' ', $parts);

        return $this;
    }

    /**
     * Set custom cron expression
     * @param string $expression
     * @return self
     */
    public function cron(string $expression): self
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * Set the callback for the task
     * @param callable $callback
     * @return self
     */
    public function call(callable $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Build and return the CronTask
     * @return CronTask
     * @throws CronException
     */
    public function build(): CronTask
    {
        if ($this->callback === null) {
            throw new CronException("Task '{$this->name}' must have a callback. Use call() method.");
        }

        if ($this->expression === null) {
            throw new CronException("Task '{$this->name}' must have a schedule. Use methods like daily(), hourly(), etc.");
        }

        return new CronTask($this->name, $this->expression, $this->callback);
    }

    /**
     * Get the cron expression
     * @return string|null
     */
    public function getExpression(): ?string
    {
        return $this->expression;
    }
}
