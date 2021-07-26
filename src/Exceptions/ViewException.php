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
 * @since 2.5.0
 */

namespace Quantum\Exceptions;

/**
 * Class ViewException
 * @package Quantum\Exceptions
 */
class ViewException extends \Exception
{

    /**
     * Direct view call message
     */
    const DIRECT_VIEW_INSTANCE = 'Views can not be instantiated directly, use `{%1}` class instead';

    /**
     * View file not found message
     */
    const LAYOUT_NOT_SET = 'Layout is not set';

    /**
     * View file not found message
     */
    const VIEW_FILE_NOT_FOUND = 'File `{%1}.php` does not exists';

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ViewException
     */
    public static function directInstantiation(string $name): ViewException
    {
        return new static(_message(self::DIRECT_VIEW_INSTANCE, $name), E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\ViewException
     */
    public static function noLayoutSet(): ViewException
    {
        return new static(self::LAYOUT_NOT_SET, E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ViewException
     */
    public static function fileNotFound(string $name): ViewException
    {
        return new static(_message(self::VIEW_FILE_NOT_FOUND, $name), E_ERROR);
    }


}
