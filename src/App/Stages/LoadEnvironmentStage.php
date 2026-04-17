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

use Quantum\Environment\Exceptions\EnvException;
use Quantum\App\Contracts\BootStageInterface;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Environment\Environment;
use Quantum\App\AppContext;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class LoadEnvironmentStage
 * @package Quantum\App
 */
class LoadEnvironmentStage implements BootStageInterface
{
    /**
     * @throws EnvException|DiException|BaseException|ReflectionException
     */
    public function process(AppContext $context): void
    {
        $environment = new Environment();

        $environment->load(new Setup('config', 'env'));

        Di::set(Environment::class, $environment);
    }
}
