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
/**
 * Directory separator
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
/**
 * Base directory of project.
 */
if (!defined('BASE_DIR')) {
    define('BASE_DIR', __DIR__ . DS . '..');
}

/**
 * Logs direcroty
 */
const LOGS_DIR = BASE_DIR . DS . 'logs';

/**
 * Vendor directory.
 */
const VENDOR_DIR = BASE_DIR . DS . 'vendor';

/**
 * Framework Core directory.
 */
const CORE_DIR = VENDOR_DIR . DS . 'quantum' . DS . 'framework' . DS . 'src';

/**
 * Core helpers directory.
 */
const HELPERS_DIR = CORE_DIR . DS . 'Helpers';

/**
 * Libraries directory.
 */
const LIBRARIES_DIR = CORE_DIR . DS . 'Libraries';

/**
 * Modules directory.
 */
const MODULES_DIR = BASE_DIR . DS . 'modules';

/**
 * Public directory.
 */
const PUBLIC_DIR = BASE_DIR . DS . 'public';

/**
 * Assets directory.
 */
const ASSETS_DIR = PUBLIC_DIR . DS . 'assets';

/**
 * Upload directory.
 */
const UPLOADS_DIR = PUBLIC_DIR . DS . 'uploads';
