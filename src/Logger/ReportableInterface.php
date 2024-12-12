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
 * @since 2.9.5
 */

namespace Quantum\Logger;

/**
 * Interface ReportableInterface
 * @package Quantum\Contracts
 */
interface ReportableInterface
{

    /**
     * Reports the message
     * @param string $level
     * @param mixed $message
     * @param array|null $context
     */
    public function report(string $level, $message, ?array $context = []);

}