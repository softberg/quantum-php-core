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
 * @since 3.0.0
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
    public static function noLayoutSet(): self
    {
        return new self(
            ExceptionMessages::LAYOUT_NOT_SET,
            E_ERROR
        );
    }

    /**
     * @return ViewException
     */
    public static function viewNotRendered(): self
    {
        return new self(
            ExceptionMessages::VIEW_NOT_RENDERED_YET,
            E_ERROR
        );
    }
}
