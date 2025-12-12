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
 * @since 2.9.9
 */

namespace Quantum\Paginator\Exceptions;

use Quantum\Paginator\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class PaginatorException
 * @package Quantum\Paginator
 */
class PaginatorException extends BaseException
{

    /**
     * @param string $type
     * @param $missingParam
     * @return PaginatorException
     */
    public static function missingRequiredParams(string $type, $missingParam): PaginatorException
    {
        return new static(_message(ExceptionMessages::MISSING_REQUIRED_PARAMS, [$missingParam, ucfirst($type)]), E_WARNING);
    }
} 