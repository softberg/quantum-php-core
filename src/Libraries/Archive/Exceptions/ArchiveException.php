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
 * @since 2.9.9
 */

namespace Quantum\Libraries\Archive\Exceptions;

use Quantum\Libraries\Archive\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class CacheException
 * @package Quantum\Libraries\Archive
 */
class ArchiveException extends BaseException
{
    /**
     * @param string $name
     * @return ArchiveException
     */
    public static function cantOpen(string $name): ArchiveException
    {
        return new static(_message(ExceptionMessages::CANT_OPEN, $name), E_WARNING);
    }

    /**
     * @return ArchiveException
     */
    public static function missingArchiveName(): ArchiveException
    {
        return new static(ExceptionMessages::NAME_NOT_SET, E_WARNING);
    }
}