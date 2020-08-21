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
 * @category Exceptions
 */
class ExceptionMessages
{

    const APP_KEY_MISSING = 'APP KEY is missing';

    /**
     * Module not found message
     */
    const MODULE_NOT_FOUND = 'Module `{%1}` not found';

    /**
     * Incorrect method message
     */
    const INCORRECT_METHOD = 'Incorrect Method `{%1}`';

    /**
     * Repetitive route message with same method
     */
    const REPETITIVE_ROUTE_SAME_METHOD = 'Repetitive Routes with same method `{%1}`';

    /**
     * Repetitive route message with in different modules message
     */
    const REPETITIVE_ROUTE_DIFFERENT_MODULES = 'Repetitive Routes in different modules';

    /**
     * Route is not a closure message
     */
    const ROUTES_NOT_CLOSURE = 'Route is not a closure';

    /**
     * Route not found message
     */
    const ROUTE_NOT_FOUND = 'Route Not Found';

    /**
     * Middleware not found message
     */
    const MIDDLEWARE_NOT_FOUND = 'Middleware `{%1}` not found';

    /**
     * Middleware not defined message
     */
    const MIDDLEWARE_NOT_DEFINED = 'Middleware `{%1}` not defined';

    /**
     * Middleware not handled correctly
     */
    const MIDDLEWARE_NOT_HANDLED = 'Middleware `{%1}` not handled correctly';

    /**
     * Controller not found message
     */
    const CONTROLLER_NOT_FOUND = 'Controller `{%1}` not found';

    /**
     * Controller not defined message
     */
    const CONTROLLER_NOT_DEFINED = 'Controller {%1} not defined';

    /**
     * Action not defined message
     */
    const ACTION_NOT_DEFINED = 'Action `{%1}` not defined';

    /**
     * Duplicate hook implementer message
     */
    const DUPLICATE_HOOK_IMPLEMENTER = 'Duplicate Hook implementer was detected';

    /**
     * Undeclared hook name message
     */
    const UNDECLARED_HOOK_NAME = 'The Hook `{%1}` was not declared';

    /**
     * Setup not provided to load
     */
    const SETUP_NOT_PROVIDED = '{%1} setup not provided';

    /**
     * Config file not found message
     */
    const CONFIG_FILE_NOT_FOUND = 'Config file `{%1}` does not exists';

    /**
     * Incorrect config message
     */
    const INCORRECT_CONFIG = 'The structure of config is not correct';

    /**
     * Config collision message
     */
    const CONFIG_COLLISION = 'Config key `{%1}` is already in use';

    /**
     * View file not found message
     */
    const VIEW_FILE_NOT_FOUND = 'File `{%1}.php` does not exists';

    /**
     * View file not found message
     */
    const LAYOUT_NOT_SET = 'Layout is not set';

    /**
     * Invalid response status message
     */
    const INVALID_RESPONSE_STATUS = 'A valid response status line was not found in the provided string';

    /**
     * CSFT token not found message
     */
    const CSRF_TOKEN_NOT_FOUND = 'CSRF Token is missing';

    /**
     * JWT payload not found message
     */
    const JWT_PAYLOAD_NOT_FOUND = 'JWT payload is missing';

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
    const INAPPROPRIATE_PROPERTY = 'Inappropriate property `{%1}` for fillable object';

    /**
     * Misconfigured session handler  message
     */
    const MISCONFIGURED_SESSION_HANDLER = 'Session handler is not properly configured';

    /**
     * Undefined database session table
     */
    const UNDEFINED_SESSION_TABLE = 'Session table `{%1}` does not exists in database';

    /**
     * Direct model call message
     */
    const DIRECT_MODEL_CALL = 'Models can not be called directly, use `{%1}` class instead';

    /**
     * Direct service call message
     */
    const DIRECT_SERVICE_CALL = 'Services can not be called directly, use `{%1}` class instead';

    /**
     * Direct view call message
     */
    const DIRECT_VIEW_INCTANCE = 'Views can not be instantiated directly, use `{%1}` class instead';

    /**
     * Model not found message
     */
    const MODEL_NOT_FOUND = 'Model `{%1}` not found';

    /**
     * Undefined model method
     */
    const UNDEFINED_MODEL_METHOD = 'Model method `{%1}` is not defined';

    /**
     * Model not instance of QtModel
     */
    const NOT_INSTANCE_OF_MODEL = 'Model `{%1}` is not instance of `{%2}`';

    /**
     * Model does not have table property defined
     */
    const MODEL_WITHOUT_TABLE_DEFINED = 'Model `{%1}` does not have $table property defined';

    /**
     * Service not found message
     */
    const SERVICE_NOT_FOUND = 'Service `{%1}` not found';

    /**
     * Model not instance of QtModel
     */
    const NOT_INSTANCE_OF_SERVICE = 'Service `{%1}` is not instance of `{%2}`';

    /**
     * Misconfigured lang config
     */
    const MISCONFIGURED_LANG_CONFIG = 'Misconfigured lang config';

    /**
     * Misconfigured default lang config
     */
    const MISCONFIGURED_LANG_DEFAULT_CONFIG = 'Misconfigured default lang config';

    /**
     * Translations not found
     */
    const TRANSLATION_FILES_NOT_FOUND = 'Translations for language `{%1}` not found';

    /**
     * Unexpected request initialization
     */
    const UNEXPECTED_REQUEST_INITIALIZATION = 'HTTP Request can not be initialized outside of the core';

    /**
     * Undefined method
     */
    const UNDEFINED_METHOD = 'The method `{%1}` is not defined';

    /**
     * Open SSL Public key not created yet
     */
    const OPENSSL_PUBLIC_KEY_NOT_CREATED = 'Public key not created yet';

    /**
     * Open SSL Private key not created yet
     */
    const OPENSSL_PRIVATE_KEY_NOT_CREATED = 'Private key not created yet';

    /**
     * Open SSL Public key is not provided
     */
    const OPENSSL_PUBLIC_KEY_NOT_PROVIDED = 'Public key is not provided';

    /**
     * Open SSL Private key is not provided
     */
    const OPENSSL_PRIVATE_KEY_NOT_PROVIDED = 'Private key is not provided';

    /**
     * Open SSL chiper is invalid
     */
    const OPENSSEL_INVALID_CIPHER = 'The cipher is invalid';

    /**
     * Open SSL config not found
     */
    const OPENSSEL_CONFIG_NOT_FOUND = 'Could not load openssl.cnf properly.';

    /**
     * Misconfigured session handler  message
     */
    const MISCONFIGURED_AUTH_CONFIG = 'Auth config is not properly configured';

    /**
     * Incorrect auth credentials  message
     */
    const INACTIVE_ACCOUNT = 'The account is not activated';

    /**
     * Incorrect auth credentials  message
     */
    const INCORRECT_AUTH_CREDENTIALS = 'Incorrect credentials';

    /**
     * Unauthorized request  message
     */
    const UNAUTHORIZED_REQUEST = 'Unauthorized request';

    /**
     * Non unique value message
     */
    const NON_UNIQUE_VALUE = 'The {%1} field needs to have unique value';

    /**
     * Non existing value message
     */
    const NON_EXISTING_RECORD = 'There is no record matched to {%1}';

    /**
     * Non equal values message
     */
    const NON_EQUAL_VALUES = 'Values are not equal';

    /**
     * Upload file not found message
     */
    const UPLOADED_FILE_NOT_FOUND = 'Cannot find uploaded file identified by key `{%1}`';

    /**
     * 
     */
    const DIRECTORY_NOT_EXIST = 'Directory `{%1}` does not exists';

    /**
     * 
     */
    const DIRECTORY_NOT_WRITABLE = 'Directory `{%1}` not writable';

    /**
     * 
     */
    const FILE_NOT_UPLOADED = 'The uploaded file was not sent with a POST request';

    /**
     * 
     */
    const FILE_ALREADY_EXISTS = 'File already exists';

}
