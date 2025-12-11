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

namespace Quantum\Libraries\Auth\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Libraries\Auth
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    const INCORRECT_USER_SCHEMA = 'User schema does not contains all key fields.';

    const VERIFICATION_CODE_EXPIRED = 'Verification code expired.';

    const INCORRECT_VERIFICATION_CODE = 'Incorrect verification code.';

    const INACTIVE_ACCOUNT = 'The account is not activated.';

    const INCORRECT_CREDENTIALS = 'Incorrect credentials.';
}