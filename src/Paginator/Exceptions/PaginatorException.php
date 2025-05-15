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
 * @since 2.9.7
 */

namespace Quantum\Paginator\Exceptions;

use Quantum\App\Exceptions\BaseException;

/**
 * Class PaginatorException
 * @package Quantum\Paginator
 */
class PaginatorException extends BaseException
{
    /**
     * @param string $type
     * @param array $missingParams
     * @return PaginatorException
     */
    public static function missingRequiredParams(string $type, array $missingParams): PaginatorException
    {
        return new static(
            t('exception.paginator_missing_params', [
                ucfirst($type),
                implode(', ', $missingParams)
            ]),
            E_WARNING
        );
    }
} 