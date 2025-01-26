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

namespace Quantum\Exceptions;

/**
 * Class ViewException
 * @package Quantum\Exceptions
 */
class ViewException extends BaseException
{
    /**
     * @param string $name
     * @return ViewException
     */
    public static function directInstantiation(string $name): ViewException
    {
        return new static(t('exception.direct_view_instance', $name), E_WARNING);
    }

    /**
     * @return ViewException
     */
    public static function noLayoutSet(): ViewException
    {
        return new static(t('exception.layout_not_set'), E_ERROR);
    }
}
