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

namespace Quantum\App\Traits;

use Quantum\Config\Exceptions\ConfigException;
use Quantum\Loader\Exceptions\LoaderException;
use Quantum\Di\Exceptions\DiException;
use Quantum\ResourceCache\ViewCache;
use Quantum\Debugger\Debugger;
use Quantum\Http\Response;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Trait WebAppTrait
 * @package Quantum\App
 */
trait WebAppTrait
{
    /**
     * @throws DiException|ReflectionException
     */
    private function initializeDebugger(): void
    {
        if (!Di::isRegistered(Debugger::class)) {
            Di::register(Debugger::class);
        }

        $debugger = Di::get(Debugger::class);
        $debugger->initStore();
    }

    /**
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException|LoaderException
     */
    private function setupViewCache(): ViewCache
    {
        if (!Di::isRegistered(ViewCache::class)) {
            Di::register(ViewCache::class);
        }

        $viewCache = Di::get(ViewCache::class);

        if ($viewCache->isEnabled()) {
            $viewCache->setup();
        }

        return $viewCache;
    }

    /**
     * @throws ConfigException|LoaderException|DiException|ReflectionException
     */
    private function handleCors(Response $response): void
    {
        if (!config()->has('cors')) {
            config()->import(new Setup('config', 'cors'));
        }

        foreach (config()->get('cors') as $key => $value) {
            $response->setHeader($key, (string) $value);
        }
    }
}
