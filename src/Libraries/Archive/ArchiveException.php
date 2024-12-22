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

namespace Quantum\Libraries\Archive;

use Quantum\Exceptions\AppException;

/**
 * Class CacheException
 * @package Quantum\Libraries\Archive
 */
class ArchiveException extends AppException
{
    /**
     * @param string $name
     * @return ArchiveException
     */
    public static function cantOpen(string $name): ArchiveException
    {
        return new static(t('exception.cant_open', $name), E_WARNING);
    }

}
