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
 * @since 2.9.6
 */

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\ServiceException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;

/**
 * Gets the FileSystem handler
 * @param string|null $adapter
 * @return FileSystem
 * @throws AuthException
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 * @throws ServiceException
 */
function fs(?string $adapter = null): FileSystem
{
    return FileSystemFactory::get($adapter);
}