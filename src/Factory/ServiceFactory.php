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

namespace Quantum\Factory;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Mvc\Qt_Service;

/**
 * ServiceFactory Class
 *
 * @package Quantum
 * @category Factory
 */
Class ServiceFactory extends Factory
{
/**
     * Get Service
     *
     * @param string $serviceClass
     * @return object
     * @throws \Exception
     */
    public function get($serviceClass)
    {
        $exceptions = [
            ExceptionMessages::SERVICE_NOT_FOUND,
            ExceptionMessages::NOT_INSTANCE_OF_SERVICE
        ];

        return $this->getInstance($serviceClass, Qt_Service::class, $exceptions);
    }

}
