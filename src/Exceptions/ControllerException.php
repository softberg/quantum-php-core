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
 * @since 2.3.0
 */

namespace Quantum\Exceptions;

/**
 * ControllerException class
 * 
 * @package Quantum
 * @category Exceptions
 */
class ControllerException extends \Exception
{
    /**
     * Controller not found message
     */
    const CONTROLLER_NOT_FOUND = 'Controller `{%1}` not found';

    /**
     * Controller not defined message
     */
    const CONTROLLER_NOT_DEFINED = 'Controller {%1} not defined';

    /**
     * Action not defined message
     */
    const ACTION_NOT_DEFINED = 'Action `{%1}` not defined';

    /**
     * Undefined method
     */
    const UNDEFINED_METHOD = 'The method `{%1}` is not defined';
}
