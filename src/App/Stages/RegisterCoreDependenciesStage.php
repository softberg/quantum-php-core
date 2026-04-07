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
use Quantum\App\AppContext;
use Quantum\Di\Di;

/**
 * Class RegisterCoreDependenciesStage
 * @package Quantum\App
 */
class RegisterCoreDependenciesStage implements BootStageInterface
{
    public function process(AppContext $context): void
    {
        $file = dirname(__DIR__) . DS . 'Config' . DS . 'dependencies.php';

        $coreDependencies = (is_file($file) && is_array($deps = require $file)) ? $deps : [];

        Di::registerDependencies($coreDependencies);
    }
}
