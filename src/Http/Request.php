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
 * Request Class
 * 
 * Receive requests 
 * 
 * @package Quantum
 * @subpackage Http
 * @category Http
 */
class Request extends HttpRequest {

    /**
     * Get
     * 
     * Responsible for get type requests
     * 
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public static function get($key, $default = NULL) {
        return parent::getParam(INPUT_GET, $key, $default);
    }

    /**
     * Post
     * 
     * Responsible for post type requests
     * 
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public static function post($key, $default = NULL) {
        return parent::getParam(INPUT_POST, $key, $default);
    }
	
	/**
     * Any
     * 
     * Responsible for any type of requests
     * 
     * @param string $key
     * @return mixed
     */
	public static function any($key) {
        $allParams = parent::getAllParams();
		return isset($allParams[$key]) ? $allParams[$key] : NULL;
    }

    /**
     * Gets all params
     * 
     * @return array
     */
    public static function all() {
        return parent::getAllParams();
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
     * Gets Сross Site Request Forgery Token
     * 
     * @return string
     * @throws \Exception When Token not found
     */
    public static function getCSRFToken() {
        $allHeaders = array_change_key_case(parent::getHeaders(), CASE_UPPER);

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
        $allHeaders = array_change_key_case(parent::getHeaders(), CASE_UPPER);

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

    /**
     * Get current route
     * 
     * Gets the current route
     * 
     * @return string
     */
    public static function getCurrentRoute() {
        return ltrim($_SERVER['REQUEST_URI'], '/');
    }

}
