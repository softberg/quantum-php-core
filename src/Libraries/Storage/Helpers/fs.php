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
 * @since 2.9.7
 */

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Di\Exceptions\DiException;


/**
 * Gets the FileSystem handler
 * @param string|null $adapter
 * @return FileSystem
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function fs(?string $adapter = null): FileSystem
{
    return FileSystemFactory::get($adapter);
}