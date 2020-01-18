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
 * @since 1.6.0
 */

namespace Quantum\Mvc;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Factory\ServiceFactory;

/**
 * Base Service Class
 *
 * Qt_Service class is a base abstract class that every service should extend,
 *
 * @package Quantum
 * @subpackage MVC
 * @category MVC
 */
abstract class Qt_Service
{
    
    /**
     * Class constructor
     */
    public final function __construct() {}
}
