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
use Dflydev\DotAccessData\Data;
use Quantum\Loader\Loader;

/**
 * Language class
 *
 * @package Quantum
 * @subpackage Libraries.Lang
 * @category Libraries
 */
class Lang
{

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
    private static $translations = [];

    /**
     * Initiates the Lang
     *
     * @param string $lang
     * @throws \Exception
     */
    public static function init($lang = null)
    {
        $languages = get_config('langs');

        if (!$languages) {
            throw new \Exception(ExceptionMessages::MISCONFIGURED_LANG_CONFIG);
        }

        if (empty($lang) && !get_config('lang_default')) {
            throw new \Exception(ExceptionMessages::MISCONFIGURED_LANG_DEFAULT_CONFIG);
        }

        if (empty($lang) || !in_array($lang, $languages)) {
            $lang = get_config('lang_default');
        }

        self::set($lang);
        self::loadDir($lang);
    }

    /**
     * Loads directory of translation files
     *
     * @param string $dirName
     * @throws \Exception
     */
    public function loadDir($dirName)
    {
        $dirPath = modules_dir() . DS . current_module() . '/Views/lang/' . $dirName;

        if(is_dir($dirPath)) {
            $files = glob($dirPath . "/*.php");
            if (count($files) == 0) {
                throw new \Exception(_message(ExceptionMessages::TRANSLATION_FILES_NOT_FOUND, $dirName));
            }

            foreach ($files as $file) {
                $fileName = pathinfo($file)['filename'];

                $setup = (object)[
                    'module' => current_module(),
                    'env' => 'Views' . DS . 'lang' . DS . $dirName,
                    'fileName' => $fileName,
                    'exceptionMessage' => ExceptionMessages::TRANSLATION_FILES_NOT_FOUND

                ];

                $loader = new Loader($setup, false);

                self::load($loader, $fileName);
            }
        }
    }

    /**
     * Loads lang file
     *
     * @param Loader $loader
     * @param string $fileName
     * @throws \Exception
     */
    public static function load(Loader $loader, $fileName)
    {
        self::$translations[$fileName] = $loader->load();
    }

    /**
     * Sets current language
     *
     * @param string $lang
     * @return void
     */
    public static function set($lang)
    {
        self::$currentLang = $lang;
    }

    /**
     * Gets the current language
     *
     * @return string
     */
    public static function get()
    {
        return self::$currentLang;
    }

    /**
     * Gets the whole translations of current languge
     *
     * @return array
     */
    public static function getTranslations()
    {
        return self::$translations;
    }

    /**
     * Gets the translation by given key
     *
     * @param $key
     * @param mixed $params
     * @return array|mixed|null|string
     */
    public static function getTranslation($key, $params = null)
    {
        $data = new Data(self::$translations);
        if ($data->has($key)) {
            if (!is_null($params)) {
                return _message($data->get($key), $params);
            } else {
                return $data->get($key);
            }
        } else {
            return $key;
        }
    }

}
