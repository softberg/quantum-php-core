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
 * @since 2.0.0
 */

namespace Quantum\Hooks;

use Quantum\Exceptions\HookException;

/**
 * HookManager Class
 * 
 * Provides a mechanism to extend the core.
 * 
 * @package Quantum
 * @subpackage Hooks
 * @category Hooks
 */
class HookManager {

    /**
     * Call method
     * 
     * @param string $hookName
     * @param mixed $args
     * @param string $alternativePath
     * @return mixed
     * @throws HookException When Hook not found
     */
    public static function call($hookName, $args = [], $alternativePath = null) {
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
                throw new HookException(_message(HookException::UNDECLARED_HOOK_NAME, $hookName));
            }
        }
    }

    /**
     * hasImplementer
     * 
     * @param string $hookName
     * @return string Implementer class name
     * @throws HookException When duplicate hook name detected
     */
    private static function hasImplementer($hookName) {
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
            throw new HookException(HookException::DUPLICATE_HOOK_IMPLEMENTER);
        }

        return $hookImplementer;
    }

    /**
     * hasDefaultImplementer 
     * 
     * @param string $hookName
     * @param string $alternativePath
     * @return bool|string
     */
    private static function hasDefaultImplementer($hookName, $alternativePath = null) {
        $classPath = $alternativePath ? $alternativePath : '\\Quantum\\Hooks\\HookDefaults';
        $class = new \ReflectionClass($classPath);

        if ($class->hasMethod($hookName)) {
            return $class->getName();
        }

        return false;
    }

}
