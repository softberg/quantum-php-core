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
use Quantum\Environment\Environment;
use Quantum\App\AppContext;
use Quantum\Loader\Setup;

/**
 * Class LoadEnvironmentStage
 * @package Quantum\App
 */
class LoadEnvironmentStage implements BootStageInterface
{
    public function process(AppContext $context): void
    {
        $environment = Environment::getInstance();

        if ($context->isConsoleMode()) {
            $environment->setMutable(true);
        }

        $environment->load(new Setup('config', 'env'));
    }
}
