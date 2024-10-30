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
 * @since 2.9.0
 */

use Quantum\Environment\Server;


/**
 * Gets user IP
 * @return string|null
 */
function get_user_ip(): ?string
{
    $server = Server::getInstance();

    if ($server->get('HTTP_CLIENT_IP')) {
        $user_ip = $server->get('HTTP_CLIENT_IP');
    } elseif ($server->get('HTTP_X_FORWARDED_FOR')) {
        $user_ip = $server->get('HTTP_X_FORWARDED_FOR');
    } else {
        $user_ip = $server->get('REMOTE_ADDR');
    }

    return $user_ip;
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

        $headers = [];

        foreach ($data as $key => $value) {
            if (substr($key, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($key, 5))))] = $value;
            }
        }

        return $headers;
    }

}
