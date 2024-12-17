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

use Quantum\Environment\Server;


/**
 * Gets user IP
 * @return string|null
 */
function get_user_ip(): ?string
{
    $server = Server::getInstance();

    return $server->get('HTTP_CLIENT_IP')
        ?? $server->get('HTTP_X_FORWARDED_FOR')
        ?? $server->get('REMOTE_ADDR');
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
        $server = Server::getInstance();
        $data = $server->all();

        if (empty($data)) {
            return [];
        }

        return array_reduce(array_keys($data), function ($headers, $key) use ($data) {
            if (strpos($key, 'HTTP_') === 0) {
                $formattedKey = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$formattedKey] = $data[$key];
            }
            return $headers;
        }, []);
    }
}
