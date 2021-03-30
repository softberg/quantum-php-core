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
 * @since 2.0.0
 */
use Quantum\Environment\Server;

if (!function_exists('get_user_ip')) {

    /**
     * Gets user IP
     * @return string|null
     */
    function get_user_ip()
    {
        $server = new Server();

        $user_ip = null;

        if ($server->get('HTTP_CLIENT_IP')) {
            $user_ip = $server->get('HTTP_CLIENT_IP');
        } elseif ($server->get('HTTP_X_FORWARDED_FOR')) {
            $user_ip = $server->get('HTTP_X_FORWARDED_FOR');
        } else {
            $user_ip = $server->get('REMOTE_ADDR');
        }

        return $user_ip;
    }

}

if (!function_exists('getallheaders')) {

    /** 	
     * Get all headers	
     * Built in PHP function synonym of apache_request_headers() 
     * Declaring here for Nginx server
     * @return array	
     */
    function getallheaders()
    {
        $server = new Server();

        $data = $server->all();

        if (!empty($data)) {
            return [];
        }

        $headers = [];

        foreach ($data as $key => $value) {
            if (substr($key, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value;
            }
        }

        return $headers;
    }

}

if (!function_exists('parse_raw_http_request')) {

    /**
     * Parses raw http request
     * @param mixed $input
     * @return mixed
     */
    function parse_raw_http_request($input)
    {
        $contentType = (string) (new Server)->contentType();

        $encoded_data = [];

        preg_match('/boundary=(.*)$/', $contentType, $matches);

        if (count($matches) > 0) {
            $boundary = $matches[1];
            $blocks = preg_split("/-+$boundary/", $input);

            if (is_array($blocks)) {
                array_pop($blocks);

                foreach ($blocks as $id => $block) {
                    if (empty($block))
                        continue;
                    if (strpos($block, 'application/octet-stream') !== false) {
                        preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
                        if (count($matches) > 0) {
                            $encoded_data['files'][$matches[1]] = isset($matches[2]) ? $matches[2] : '';
                        }
                    } else {
                        preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
                        if (count($matches) > 0) {
                            $encoded_data[$matches[1]] = isset($matches[2]) ? $matches[2] : '';
                        }
                    }
                }
            }
        }

        return $encoded_data;
    }

}
