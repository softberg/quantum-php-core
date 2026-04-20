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

use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Contracts\BootStageInterface;
use Quantum\Logger\Factories\LoggerFactory;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Tracer\ErrorHandler;
use Quantum\App\AppContext;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class SetupErrorHandlerStage
 * @package Quantum\App
 */
class SetupErrorHandlerStage implements BootStageInterface
{
    /**
     * @throws ConfigException|DiException|BaseException|ReflectionException
     */
    public function process(AppContext $context): void
    {
        if (!Di::isRegistered(ErrorHandler::class)) {
            Di::register(ErrorHandler::class);
        }

        Di::get(ErrorHandler::class)->setup(LoggerFactory::get());
    }
}
