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

use Quantum\Environment\Server;

/**
 * Gets Server instance
 * @return Server
 */
function server(): Server
{
    return Server::getInstance();
}

/**
 * Gets user IP
 * @return string|null
 */
function get_user_ip(): ?string
{
    return Server::getInstance()->ip();
}

if (!function_exists('getallheaders')) {

    /**
     * Get all headers
     * Built-in PHP function synonym of apache_request_headers()
     * Declaring here for Nginx server
     * @return array
     */
    function getallheaders(): array
    {
        return Server::getInstance()->getAllHeaders();
    }
}
