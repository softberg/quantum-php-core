<?php

declare(strict_types=1);

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

namespace Quantum\Logger\Contracts;

/**
 * Interface ReportableInterface
 * @package Quantum\Logger
 */
interface ReportableInterface
{
    /**
     * Reports a message
     * @param string $message
     * @param array<string, mixed>|null $context
     * @return void
     */
    public function report(string $level, string $message, ?array $context = []): void;

}
