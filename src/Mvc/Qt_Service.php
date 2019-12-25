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
use Quantum\Factory\ModelFactory;
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
     *
     * @return void
     * @throws \Exception When called directly
     */
    public final function __construct()
    {
        if (get_caller_class(2) != ServiceFactory::class) {
            throw new \Exception(_message(ExceptionMessages::DIRECT_SERVICE_CALL, [ServiceFactory::class]));
        }
    }
}
