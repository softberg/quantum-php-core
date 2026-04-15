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

use Quantum\Module\Exceptions\ModuleException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\App\Contracts\BootStageInterface;
use Quantum\Di\Exceptions\DiException;
use Quantum\Router\RouteCollection;
use Quantum\Router\RouteBuilder;
use Quantum\Module\ModuleLoader;
use Quantum\App\AppContext;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class LoadModulesStage
 * @package Quantum\App
 */
class LoadModulesStage implements BootStageInterface
{
    /**
     * @throws ModuleException|RouteException|DiException|ReflectionException
     */
    public function process(AppContext $context): void
    {
        if (!Di::isRegistered(ModuleLoader::class)) {
            Di::register(ModuleLoader::class);
        }

        $moduleLoader = Di::get(ModuleLoader::class);

        $builder = new RouteBuilder();

        $collection = $builder->build(
            $moduleLoader->loadModulesRoutes(),
            $moduleLoader->getModuleConfigs()
        );

        Di::set(RouteCollection::class, $collection);
    }
}
