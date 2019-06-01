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
 * @since 1.0.0
 */

namespace Quantum\Http;

use Quantum\Exceptions\ExceptionMessages;

/**
 * HttpRequest Class
 * 
 * Abstract base http request class
 * 
 * @package Quantum
 * @subpackage Http
 * @category Http
 */
abstract class HttpRequest {

    /**
     * Request headers
     * 
     * @var array
     */
    private static $__headers = [];

    /**
     * Request body
     * 
     * @var array 
     */
    private static $__request = [];

    /**
     * Initialize the Request
     * 
     * @throws \Exception When called outside of MvcManager
     */
    public static function init() {
        if(get_caller_class() !== 'Quantum\Mvc\MvcManager') {
            throw new \Exception(ExceptionMessages::UNEXPECTED_REQUEST_INITIALIZATION);
        }
        
        $getParams = !empty($_GET) ? filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY) : [];
        $postParams = !empty($_POST) ? filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY) : [];
        $inputParams = self::getInputParams(); 

        self::$__request = array_merge(
                self::$__request, 
                $getParams, 
                $postParams,
                $inputParams
        );
        
        self::$__headers = getallheaders();
    }

    /**
     * Get Input parameters sent via PUT, PATCH or DELETE methods
     * 
     * @return array
     */
    private static function getInputParams() {
        $inputParams = [];
        
        if ($_SERVER['REQUEST_METHOD'] == 'PUT' || 
            $_SERVER['REQUEST_METHOD'] == 'PATCH' || 
            $_SERVER['REQUEST_METHOD'] == 'DELETE') {

            $input = file_get_contents('php://input');

            if (isset($_SERVER['CONTENT_TYPE'])) {
                switch ($_SERVER['CONTENT_TYPE']) {
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

        return $inputParams;
    }

    /**
     * Set
     * 
     * Set new key/value pair into request
     * 
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value) {
        self::$__request[$key] = $value;
    }

    /**
     * Get
     * 
     * Retrieves data from request by given key
     * 
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public static function get($key, $default = NULL) {
        return isset(self::$__request[$key]) ? self::$__request[$key] : $default;
    }

    /**
     * Gets all request parameters
     * 
     * @return array
     */
    public static function all() {
        return self::$__request;
    }

    /**
     * Checks to see if request contains file
     * 
     * @return bool
     */
    public static function hasFile($key) {
        if (isset($_FILES[$key]) === false || (isset($_FILES[$key]['error']) && $_FILES[$key]['error'] != 0)) {
            return false;
        }
        return true;
    }
    
    /**
     * Get all request headers
     * 
     * @return array
     */
    public function allHeaders() {
        return self::$__headers;
    }

    /**
     * Gets Сross Site Request Forgery Token
     * 
     * @return string
     * @throws \Exception When Token not found
     */
    public static function getCSRFToken() {
        $allHeaders = array_change_key_case(self::$__headers, CASE_UPPER);

        if (array_key_exists('X-CSRF-TOKEN', $allHeaders)) {
            return $allHeaders['X-CSRF-TOKEN'];
        } else {
            throw new \Exception(ExceptionMessages::CSRF_TOKEN_NOT_FOUND);
        }
    }

    /**
     * Gets Authorization Bearer token
     * 
     * @return string
     * @throws \Exception
     */
    public function getAuthorizationBearer() {
        $allHeaders = array_change_key_case(self::$__request, CASE_UPPER);

        if (array_key_exists('AUTHORIZATION', $allHeaders)) {
            if (preg_match('/Bearer\s(\S+)/', $allHeaders['AUTHORIZATION'], $matches)) {
                return $matches[1];
            }
        } else {
            throw new \Exception(ExceptionMessages::AUTH_BEARER_NOT_FOUND);
        }
    }

    /**
     * isAjax
     * 
     * Checks to see if request was ajax request
     * 
     * @return boolean
     */
    public static function isAjax() {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }

        return false;
    }

    /**
     * Get all segments
     * 
     * Gets the segments of current URI
     * 
     * @return array
     */
    public static function getAllSegments() {
        $parsed = parse_url($_SERVER['REQUEST_URI']);
        $path = $parsed['path'];
        return explode('/', $path);
    }

    /**
     * Get Segment
     * 
     * Gets the nth segment
     * 
     * @param integer $number
     * @return string|null
     */
    public static function getSegment($number) {
        $segments = self::getAllSegments();

        if (isset($segments[$number]))
            return $segments[$number];

        return NULL;
    }

}
