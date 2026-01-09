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

namespace Quantum\Libraries\Auth\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Libraries\Auth
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    public const INCORRECT_USER_SCHEMA = 'User schema does not contains all key fields.';

    public const VERIFICATION_CODE_EXPIRED = 'Verification code expired.';

    public const INCORRECT_VERIFICATION_CODE = 'Incorrect verification code.';

    public const INACTIVE_ACCOUNT = 'The account is not activated.';

    public const INCORRECT_CREDENTIALS = 'Incorrect credentials.';
}
