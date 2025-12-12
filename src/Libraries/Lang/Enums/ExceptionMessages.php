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

namespace Quantum\Libraries\Lang\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Libraries\Lang
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    const TRANSLATION_FILES_NOT_FOUND = 'Translation files not found.';

    const MISCONFIGURED_DEFAULT_LANG = 'Misconfigured lang default config.';
}