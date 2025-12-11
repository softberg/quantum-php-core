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

namespace Quantum\Environment\Enums;

/**
 * Class Env
 * @package Quantum\Environment
 */
final class Env
{
    /**
     * Production environment - live system used by end users.
     */
    public const PRODUCTION = 'production';

    /**
     * Staging environment - pre-production environment for final testing.
     */
    public const STAGING = 'staging';

    /**
     * Development environment - used by developers for active development.
     */
    public const DEVELOPMENT = 'development';

    /**
     * Testing environment - used for automated tests and quality assurance.
     */
    public const TESTING = 'testing';

    /**
     * Local environment - developer's local machine environment.
     */
    public const LOCAL = 'local';
}