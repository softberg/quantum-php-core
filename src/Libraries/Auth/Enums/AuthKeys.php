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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Auth\Enums;

/**
 * Class AuthKeys
 * @package Quantum\Libraries\Auth
 */
class AuthKeys
{
    /**
     * One time password key
     */
    const OTP = 'otp';

    /**
     * One time password expiry key
     */
    const OTP_EXPIRY = 'otpExpiry';

    /**
     * One time password token key
     */
    const OTP_TOKEN = 'otpToken';

    /**
     * Username key
     */
    const USERNAME = 'username';

    /**
     * Password key
     */
    const PASSWORD = 'password';

    /**
     * Access token key
     */
    const ACCESS_TOKEN = 'accessToken';

    /**
     * Refresh token key
     */
    const REFRESH_TOKEN = 'refreshToken';

    /**
     * Activation token key
     */
    const ACTIVATION_TOKEN = 'activationToken';

    /**
     * Reset token key
     */
    const RESET_TOKEN = 'resetToken';

    /**
     * Remember token key
     */
    const REMEMBER_TOKEN = 'rememberToken';
}