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

/**
 * Class AuthKeys
 * @package Quantum\Libraries\Auth
 */
class AuthKeys
{
    /**
     * One time password key
     */
    public const OTP = 'otp';

    /**
     * One time password expiry key
     */
    public const OTP_EXPIRY = 'otpExpiry';

    /**
     * One time password token key
     */
    public const OTP_TOKEN = 'otpToken';

    /**
     * Username key
     */
    public const USERNAME = 'username';

    /**
     * Password key
     */
    public const PASSWORD = 'password';

    /**
     * Access token key
     */
    public const ACCESS_TOKEN = 'accessToken';

    /**
     * Refresh token key
     */
    public const REFRESH_TOKEN = 'refreshToken';

    /**
     * Activation token key
     */
    public const ACTIVATION_TOKEN = 'activationToken';

    /**
     * Reset token key
     */
    public const RESET_TOKEN = 'resetToken';

    /**
     * Remember token key
     */
    public const REMEMBER_TOKEN = 'rememberToken';
}
