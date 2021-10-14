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

namespace Quantum\Hooks;

/**
 * Interface HookInterface
 * @package Quantum\Hooks
 */
interface HookInterface
{

    /**
     * Applies the instuctions defeined in hook
     */
    public function apply(): void;

}
