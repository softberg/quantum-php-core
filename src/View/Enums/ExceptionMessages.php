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
 * @since 3.0.0
 */

namespace Quantum\View\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\View
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    public const LAYOUT_NOT_SET = 'Layout is not set.';

    public const VIEW_NOT_RENDERED_YET = 'View not rendered yet.';
}
