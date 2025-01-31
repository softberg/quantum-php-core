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
 * @since 2.9.5
 */

namespace Quantum\Environment\Exceptions;

use Quantum\Exceptions\BaseException;

/**
 * Class EnvException
 * @package Quantum\Exceptions
 */
class EnvException extends BaseException
{

    /**
     * @return EnvException
     */
    public static function environmentImmutable(): EnvException
    {
        return new static(t('exception.immutable_environment'), E_ERROR);
    }

    /**
     * @return EnvException
     */
    public static function environmentNotLoaded(): EnvException
    {
        return new static(t('exception.environment_not_loaded'), E_ERROR);
    }
}
