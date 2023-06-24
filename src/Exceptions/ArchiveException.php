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
 * @since 2.9.0
 */

namespace Quantum\Exceptions;

/**
 * Class CacheException
 * @package Quantum\Exceptions
 */
class ArchiveException extends AppException
{
    /**
     * @param string $name
     * @return ArchiveException
     * @throws LangException
     */
    public static function cantOpen(string $name): ArchiveException
    {
        return new static(t('exception.cant_open', $name), E_WARNING);
    }

    /**
     * @param string $name
     * @return ArchiveException
     * @throws LangException
     */
    public static function fileNotFound(string $name): ArchiveException
    {
        return new static(t('exception.file_not_found', $name), E_WARNING);
    }
}
