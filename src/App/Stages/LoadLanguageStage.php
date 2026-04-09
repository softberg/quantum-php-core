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

use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Contracts\BootStageInterface;
use Quantum\Lang\Exceptions\LangException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Lang\Factories\LangFactory;
use Quantum\Di\Exceptions\DiException;
use Quantum\App\AppContext;
use ReflectionException;

/**
 * Class LoadLanguageStage
 * @package Quantum\App
 */
class LoadLanguageStage implements BootStageInterface
{
    /**
     * @throws LangException|ConfigException|DiException|BaseException|ReflectionException
     */
    public function process(AppContext $context): void
    {
        $lang = LangFactory::get();

        if ($lang->isEnabled()) {
            $lang->load();
        }
    }
}
