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
use Quantum\Loader\Setup;

/**
 * Class LoadAppConfigStage
 * @package Quantum\App
 */
class LoadAppConfigStage implements BootStageInterface
{
    public function process(AppContext $context): void
    {
        if (!config()->has('app')) {
            config()->import(new Setup('config', 'app'));
        }
    }
}
