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

namespace Quantum\View\Factories;

use Quantum\Renderer\Factories\RendererFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\ResourceCache\ViewCache;
use DebugBar\DebugBarException;
use Quantum\Debugger\Debugger;
use Quantum\View\QtView;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class ViewFactory
 * @package Quantum\View
 * @mixin QtView
 */
class ViewFactory
{
    private ?QtView $instance = null;

    /**
     * @throws DebugBarException
     * @throws DiException
     * @throws BaseException
     * @throws ConfigException
     * @throws ReflectionException
     */
    public static function get(): QtView
    {
        return Di::get(self::class)->resolve();
    }

    /**
     * @throws DebugBarException
     * @throws DiException
     * @throws BaseException
     * @throws ConfigException
     * @throws ReflectionException
     */
    public function resolve(): QtView
    {
        if ($this->instance === null) {
            $this->instance = new QtView(
                RendererFactory::get(),
                asset(),
                Di::get(Debugger::class),
                ViewCache::getInstance()
            );
        }

        return $this->instance;
    }
}
