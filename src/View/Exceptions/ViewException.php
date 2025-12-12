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

namespace Quantum\View\Exceptions;

use Quantum\View\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class ViewException
 * @package Quantum\View
 */
class ViewException extends BaseException
{

    /**
     * @return ViewException
     */
    public static function noLayoutSet(): ViewException
    {
        return new static(ExceptionMessages::LAYOUT_NOT_SET, E_ERROR);
    }

    /**
     * @return ViewException
     */
    public static function viewNotRendered(): ViewException
    {
        return new static(ExceptionMessages::VIEW_NOT_RENDERED_YET, E_ERROR);
    }
}
