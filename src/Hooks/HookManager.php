<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.6.0
 */

namespace Quantum\Hooks;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Di\Di;

/**
 * Class HookManager
 * @package Quantum\Hooks
 */
class HookManager
{

    /**
     * @var \Quantum\Hooks\HookManager|null
     */
    private static $instance = null;

    /**
     * @return \Quantum\Hooks\HookManager|null
     */
    public static function getInstance(): ?HookManager
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $hookName
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    public function __invoke(string $hookName)
    {
        $fs = Di::get(FileSystem::class);

        if ($fs->exists(hooks_dir() . DS . $hookName . '.php')) {
            $className = '\\Hooks\\' . ucfirst($hookName);

            if (class_exists($className, true)) {
                $this->handleHook(new $className);
            }
        }
    }

    /**
     * @param \Quantum\Hooks\HookInterface $hook
     */
    private function handleHook(HookInterface $hook)
    {
        $hook->apply();
    }

}
