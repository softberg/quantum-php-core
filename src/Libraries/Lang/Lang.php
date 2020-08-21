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

namespace Quantum\Libraries\Lang;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\LangException;
use Dflydev\DotAccessData\Data;
use Quantum\Loader\Loader;

/**
 * Language class
 * @package Quantum\Libraries\Lang
 */
class Lang
{

    /**
     * Current language
     * @var string
     */
    private static $currentLang;

    /**
     * Translations
     * @var array
     */
    private static $translations = [];

    /**
     * Lang dir
     * @var string 
     */
    private $langDir;

    /**
     * File System
     * @var FileSystem
     */
    private $fs;

    /**
     * Instance of Lang
     * @var Lang 
     */
    private static $langInstance = null;

    /**
     * Lang constructor.
     * @param string $lang
     * @throws LangException
     */
    private function __construct()
    {
        if (!config()->has('langs')) {
            throw new LangException(ExceptionMessages::MISCONFIGURED_LANG_CONFIG);
        }

        $this->fs = new FileSystem();
    }

    /**
     * GetInstance
     * @return Lang
     */
    public static function getInstance()
    {
        if (self::$langInstance === null) {
            self::$langInstance = new self();
        }

        return self::$langInstance;
    }

    /**
     * Loads translations
     * @param Loader $loader
     * @throws LangException
     */
    public function load(Loader $loader)
    {
        $langDir = modules_dir() . DS . current_module() . DS . 'Resources' . DS . 'lang' . DS . $this->getLang();

        $files = $this->fs->glob($langDir . "/*.php");

        if (is_array($files) && count($files) == 0) {
            throw new LangException(_message(ExceptionMessages::TRANSLATION_FILES_NOT_FOUND, $this->getLang()));
        }

        foreach ($files as $file) {
            $fileName = $this->fs->fileName($file);

            $setup = (object) [
                        'module' => current_module(),
                        'env' => 'Resources' . DS . 'lang' . DS . $this->getLang(),
                        'fileName' => $fileName,
                        'exceptionMessage' => ExceptionMessages::TRANSLATION_FILES_NOT_FOUND
            ];

            self::$translations[$fileName] = $loader->setup($setup)->load();
        }
    }

    /**
     * Sets current language
     * @param string $lang
     * @return $this
     * @throws LangException
     */
    public function setLang($lang)
    {
        if (empty($lang) && !config()->get('lang_default')) {
            throw new LangException(ExceptionMessages::MISCONFIGURED_LANG_DEFAULT_CONFIG);
        }

        if (empty($lang) || !in_array($lang, (array) config()->get('langs'))) {
            $lang = config()->get('lang_default');
        }

        self::$currentLang = $lang;
        return $this;
    }

    /**
     * Gets the current language
     * @return string
     */
    public function getLang()
    {
        return self::$currentLang;
    }

    /**
     * Gets the whole translations of current language
     * @return array
     */
    public function getTranslations()
    {
        return self::$translations;
    }

    /**
     * Gets the translation by given key
     * @param $key
     * @param mixed $params
     * @return string
     */
    public function getTranslation($key, $params = null)
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
