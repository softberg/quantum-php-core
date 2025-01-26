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

namespace Quantum\App\Contracts;


/**
 * Interface AppInterface
 * @package Quantum\App
 */
interface AppInterface
{
    /**
     * Starts the app
     * @return int|null
     */
    public function start(): ?int;
}