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
 * @since 1.9.5
 */

namespace Quantum\Exceptions;

/**
 * ViewException class
 * 
 * @package Quantum
 * @category Exceptions
 */
class ViewException extends \Exception
{
    /**
     * Direct view call message
     */
    const DIRECT_VIEW_INCTANCE = 'Views can not be instantiated directly, use `{%1}` class instead';

    /**
     * View file not found message
     */
    const LAYOUT_NOT_SET = 'Layout is not set';

    /**
     * View file not found message
     */
    const VIEW_FILE_NOT_FOUND = 'File `{%1}.php` does not exists';
}
