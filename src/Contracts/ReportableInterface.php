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
 * @since 2.4.0
 */

namespace Quantum\Contracts;

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
     * @param array $context
     */
    public function report(string $level, $message, array $context = []);

}