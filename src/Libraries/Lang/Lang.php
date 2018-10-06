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

namespace Quantum\Libraries\Lang;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Routes\RouteController;

/**
 * Language class
 * 
 * @package Quantum
 * @subpackage Libraries.Lang
 * @category Libraries
 */
class Lang {

    /**
     * Current language
     * 
     * @var strinng 
     */
    private static $currentLang;

    /**
     * Translations
     * 
     * @var array 
     */
    private static $translations = array();

    /**
     * Finds and loads translation files 
     * 
     * @param string $lang
     * @return void
     * @throws \Exception
     */
    public function load($lang) {
        $langDir = MODULES_DIR . DS . RouteController::$currentModule . '/Views/lang/' . $lang;

        $files = glob($langDir . "/*.php");
        if (count($files) == 0) {
            throw new \Exception(ExceptionMessages::TRANSLATION_FILES_NOT_FOUND);
        }

        foreach ($files as $file) {
            $fileInfo = pathinfo($file);
            self::$translations[$fileInfo['filename']] = require_once $file;
        }
    }

    /**
     * Sets current language 
     * 
     * @param string $lang
     * @return void
     * @throws \Exception
     */
    public static function set($lang = NULL) {
        $languages = get_config('langs');
        if (!$languages) {
            throw new \Exception(ExceptionMessages::MISCONFIGURED_LANG_CONFIG);
        }

        if (!get_config('lang_default')) {
            throw new \Exception(ExceptionMessages::MISCONFIGURED_LANG_DEFAULT_CONFIG);
        }

        if (empty($lang) || !in_array($lang, $languages)) {
            $lang = get_config('lang_default');
        }

        self::$currentLang = $lang;
        self::load($lang);
    }
    
    /**
     * Gets the current language 
     * 
     * @return string
     */
    public static function get() {
        return self::$currentLang;
    }

    /**
     * Gets the translated data
     * 
     * @return array
     */
    public static function getTranslations() {
        return self::$translations;
    }

}
