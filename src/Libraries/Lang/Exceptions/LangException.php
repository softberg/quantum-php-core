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

namespace Quantum\Libraries\Lang\Exceptions;

use Quantum\Libraries\Lang\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class LangException
 * @package Quantum\Libraries\Lang
 */
class LangException extends BaseException
{
    /**
     * @return LangException
     */
    public static function translationsNotFound(): self
    {
        return new self(
            ExceptionMessages::TRANSLATION_FILES_NOT_FOUND,
            E_WARNING
        );
    }

    /**
     * @return LangException
     */
    public static function misconfiguredDefaultConfig(): self
    {
        return new self(
            ExceptionMessages::MISCONFIGURED_DEFAULT_LANG,
            E_WARNING
        );
    }
}
