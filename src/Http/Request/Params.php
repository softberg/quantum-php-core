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
 * @since 2.4.0
 */

namespace Quantum\Http\Request;

/**
 * Trait Params
 * @package Quantum\Http\Request
 */
trait Params
{

    /**
     * Gets the GET params
     * @return array|null
     */
    private static function getParams(): ?array
    {
        $getParams = [];

        if (!empty($_GET)) {
            $getParams = filter_input_array(INPUT_GET, FILTER_DEFAULT);
        }

        return $getParams;
    }

    /**
     * Gets the POST params
     * @return array|null
     */
    private static function postParams(): ?array
    {
        $postParams = [];

        if (!empty($_POST)) {
            $postParams = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        }

        return $postParams;
    }

    /**
     * Get Input parameters sent via PUT, PATCH or DELETE methods
     * @return array
     */
    private static function getRawInputs(): array
    {
        $inputParams = [];

        if (in_array(self::$__method, ['PUT', 'PATCH', 'DELETE'])) {

            $input = file_get_contents('php://input');

            if (self::$server->contentType()) {
                switch (self::$server->contentType()) {
                    case 'application/x-www-form-urlencoded':
                        parse_str($input, $inputParams);
                        break;
                    case 'application/json':
                        $inputParams = json_decode($input);
                        break;
                    default :
                        $inputParams = parse_raw_http_request($input);
                        break;
                }
            }
        }

        return (array)$inputParams;
    }

}