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
 * @since 2.6.0
 */

namespace Quantum\Console;

/**
 * Interface CommandInterface
 * @package Quantum\Console
 */
interface CommandInterface
{
    /**
     * Executes the current command.
     */
    public function exec();

}