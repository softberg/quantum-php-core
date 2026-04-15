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

namespace Quantum\App\Stages;

use Quantum\App\Contracts\BootStageInterface;
use Quantum\Di\Exceptions\DiException;
use Quantum\Debugger\Debugger;
use Quantum\App\AppContext;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class InitDebuggerStage
 * @package Quantum\App
 */
class InitDebuggerStage implements BootStageInterface
{
    /**
     * @throws DiException|ReflectionException
     */
    public function process(AppContext $context): void
    {
        if (!Di::isRegistered(Debugger::class)) {
            Di::register(Debugger::class);
        }

        Di::get(Debugger::class)->initStore();
    }
}
