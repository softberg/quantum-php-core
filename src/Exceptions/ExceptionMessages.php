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

namespace Quantum\Exceptions;

/**
 * ExceptionMessages class
 * 
 * 
 * @package Quantum
 * @subpackage Exceptions
 * @category Exceptions
 */
class ExceptionMessages {

    /**
     * Module not found message
     */
    const MODULE_NOT_FOUND = 'Module {%1} not found';
    
    /**
     * Incorrect method message
     */
    const INCORRECT_METHOD = 'Incorrect Method {%1}';
    
    /**
     * Repetitive route message with same method
     */
    const REPETITIVE_ROUTE_SAME_METHOD = 'Repetitive Routes with same method {%1}';
    
    /**
     * Repetitive route message with in different modules message
     */
    const REPETITIVE_ROUTE_DIFFERENT_MODULES = 'Repetitive Routes in different modules';
    
    /**
     * Route not found message
     */
    const ROUTE_NOT_FOUND = 'Route Not Found';
    
    /**
     * Middleware not found message
     */
    const MIDDLEWARE_NOT_FOUND = 'Middleware {%1} not found';
    
    /**
     * Middleware not defined message
     */
    const MIDDLEWARE_NOT_DEFINED = 'Middleware {%1} not defined';
    
    /**
     * Middleware not handled correctly
     */
    const MIDDLEWARE_NOT_HANDLED = 'Middleware {%1} not handled correctly';
    
    /**
     * Controller not found message
     */
    const CONTROLLER_NOT_FOUND = 'Controller {%1} not found';
    
    /**
     * Controller not defined message
     */
    const CONTROLLER_NOT_DEFINED = 'Controller {%1} not defined';
    
    /**
     * Action not defined message
     */
    const ACTION_NOT_DEFINED = 'Action {%1} not defined';
    
    /**
     * Duplicate hook implementer message
     */
    const DUPLICATE_HOOK_IMPLEMENTER = 'Duplicate Hook implementer was detected';
    
    /**
     * Undeclared hook name message
     */
    const UNDECLARED_HOOK_NAME = 'The Hook {%1} was not declared';
    
    /**
     * Config file not found message
     */
    const CONFIG_FILE_NOT_FOUND = 'Config file does not exists';
    
    /**
     * Incorrect config message
     */
    const INCORRECT_CONFIG = 'The structure of config is not correct';
    
    /**
     * Config collision message
     */
    const CONFIG_COLLISION = 'Config key is already in use';
    
    /**
     * DB config not found message
     */
    const DB_CONFIG_NOT_FOUND = 'Database config file does not exists';
    
    /**
     * View file not found message
     */
    const VIEW_FILE_NOT_FOUND = 'File {%1}.php does not exists';
    
    /**
     * Invalid response status message
     */
    const INVALID_RESPONSE_STATUS = 'A valid response status line was not found in the provided string';
    
    /**
     * CSFT token not found message
     */
    const CSRF_TOKEN_NOT_FOUND = 'CSRF Token does not exists';
    
    /**
     * Authorization: Bearer header not found message
     */
    const AUTH_BEARER_NOT_FOUND = 'Authorization: Bearer header not found';
    
    /**
     * CSFT token not matched message
     */
    const CSRF_TOKEN_NOT_MATCHED = 'CSRF Token does not matched';
    
    /**
     * Inappropriate property message
     */
    const INAPPROPRIATE_PROPERTY = 'Inappropriate property {%1} for fillable object';
    
    /**
     * Misconfigured session handler  message
     */
    const MISCONFIGURED_SESSION_HANDLER = 'Session handler is not properly configured';
    
    /**
     * Direct model call message
     */
    const DIRECT_MODEL_CALL = 'Models can not be called directly, use "modelFactory" method instead';
	
	/**
     * Model not found message
     */
    const MODEL_NOT_FOUND = 'Model {%1} not found';
    
    /**
     * Undefined model method
     */
    const UNDEFINED_MODEL_METHOD = 'Model method {%1} is not defined';
    
      /**
     * Misconfigured lang config
     */
    const MISCONFIGURED_LANG_CONFIG = 'Misconfigured lang config';

    /**
     * Misconfigured default lang config
     */
    const MISCONFIGURED_LANG_DEFAULT_CONFIG = 'Misconfigured default lang config';
    
    /**
     * Unexpected request initialization 
     */
    const UNEXPECTED_REQUEST_INITIALIZATION = 'HTTP Request can not be initialized outside of the core';

}
