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
 * @since 3.0.0
 */

namespace Quantum\App\Adapters;

use Quantum\App\Exceptions\BaseException;
use Quantum\App\Contracts\AppInterface;
use Quantum\Di\Exceptions\DiException;
use Quantum\App\Traits\AppTrait;
use ReflectionException;

/**
 * Class AppAdapter
 * @package Quantum\App
 */
abstract class AppAdapter implements AppInterface
{
    use AppTrait;

    /**
     * @var string
     */
    private static $baseDir;

    /**
     * @throws BaseException
     * @throws DiException
     * @throws ReflectionException
     */
    public function __construct()
    {
        $this->loadCoreDependencies();
        $this->loadComponentHelperFunctions();
        $this->loadLibraryHelperFunctions();
        $this->loadAppHelperFunctions();
        $this->loadModuleHelperFunctions();
    }
}
