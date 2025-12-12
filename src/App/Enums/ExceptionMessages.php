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

namespace Quantum\App\Enums;

/**
 * Class ExceptionMessages
 * @package Quantum\App
 */
class ExceptionMessages
{
    const APP_KEY_MISSING = 'APP KEY is missing.';

    const EXECUTION_TERMINATED = 'Execution was terminated.';

    const METHOD_NOT_SUPPORTED = 'The method `{%1}` is not supported for `{%2}`.';

    const ADAPTER_NOT_SUPPORTED = 'The adapter `{%1}` is not supported.';

    const DRIVER_NOT_SUPPORTED = 'The driver `{%1}` is not supported.';

    const FILE_NOT_FOUND = 'The file `{%1}` not found.';

    const NOT_FOUND = '{%1} `{%2}` not found.';

    const NOT_INSTANCE_OF = 'The `{%1}` is not instance of `{%2}`.';

    const CANT_CONNECT = 'Can not connect to `{%1}`.';

    const MISSING_CONFIG = 'Could not load config `{%1}` properly.';

    const UNAVAILABLE_REQUEST_METHOD = 'Provided request method `{%1}` is not available.';
}