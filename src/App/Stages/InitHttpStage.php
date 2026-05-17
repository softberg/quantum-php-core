<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 3.0.0
 */

namespace Quantum\App\Stages;

use Quantum\App\Contracts\BootStageInterface;
use Quantum\Di\Exceptions\DiException;
use Quantum\App\AppContext;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class InitHttpStage
 * @package Quantum\App
 */
class InitHttpStage implements BootStageInterface
{
    /**
     * @throws DiException|ReflectionException
     */
    public function process(AppContext $context): void
    {
        if (!Di::isRegistered(Request::class)) {
            Di::register(Request::class);
        }

        if (!Di::isRegistered(Response::class)) {
            Di::register(Response::class);
        }
    }
}
