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
 * @since 2.5.0
 */

namespace Quantum\Hooks;

use Quantum\Exceptions\HookException;

/**
 * Class HookManager
 * @package Quantum\Hooks
 */
class HookManager
{

    /**
     * Finds and calls the defined class method
     * @param string $hookName
     * @param array $args
     * @param string|null $alternativePath
     * @return mixed
     * @throws \Quantum\Exceptions\HookException
     * @throws \ReflectionException
     */
    public static function call(string $hookName, array $args = [], string $alternativePath = null)
    {
        $hookImplementer = self::hasImplementer($hookName);

        if (!empty($hookImplementer)) {
            $implementerClass = '\\Hooks\\' . $hookImplementer;
            $implementer = new $implementerClass();
            $implementer->$hookName($args);
        } else {
            $defaultImplementer = self::hasDefaultImplementer($hookName, $alternativePath);

            if ($defaultImplementer) {
                return $defaultImplementer::$hookName($args);
            } else {
                throw HookException::undeclaredHookName($hookName);
            }
        }
    }

    /**
     * Finds the implementer
     * @param string $hookName
     * @return string|null
     * @throws \Quantum\Exceptions\HookException
     * @throws \ReflectionException
     */
    private static function hasImplementer(string $hookName): ?string
    {
        $classNames = get_directory_classes(BASE_DIR . DS . 'hooks');

        $duplicates = 0;

        $hookImplementer = null;

        foreach ($classNames as $className) {
            $implementerClass = '\\Hooks\\' . $className;
            if (class_exists($implementerClass, true)) {
                $class = new \ReflectionClass('\\Hooks\\' . $className);
                if ($class->implementsInterface('Quantum\\Hooks\\HookInterface')) {
                    if ($class->hasMethod($hookName)) {
                        $hookImplementer = $className;
                        $duplicates++;
                    }
                }
            }
        }

        if ($duplicates > 1) {
            throw HookException::duplicateHookImplementer();
        }

        return $hookImplementer;
    }

    /**
     * Finds default implementer
     * @param string $hookName
     * @param string|null $alternativePath
     * @return bool|string
     * @throws \ReflectionException
     */
    private static function hasDefaultImplementer(string $hookName, string $alternativePath = null)
    {
        $classPath = $alternativePath ?: '\\Quantum\\Hooks\\HookDefaults';
        $class = new \ReflectionClass($classPath);

        if ($class->hasMethod($hookName)) {
            return $class->getName();
        }

        return false;
    }

}
