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

namespace Quantum\Libraries\Encryption\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Libraries\Encryption
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    const PUBLIC_KEY_MISSING = 'Public key is not provided';

    const PRIVATE_KEY_MISSING = 'Private key is not provided';

    const INVALID_CIPHER = 'The cipher is invalid';


}