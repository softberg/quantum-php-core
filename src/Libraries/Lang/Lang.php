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
 * @since 2.8.0
 */

namespace Quantum\Libraries\Lang;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\LangException;
use Dflydev\DotAccessData\Data;
use Quantum\Loader\Loader;
use Quantum\Http\Request;
use Quantum\Loader\Setup;
use Quantum\Di\Di;

/**
 * Language class
 * @package Quantum\Libraries\Lang
 */
class Lang
{

    /**
     * Config key for defined languages
     */
    const LANGS_DEFINED = 'langs';

    /**
     * Config key for lang segment
     */
    const LANG_SEGMENT = 'lang_segment';

    /**
     * Current language
     * @var string
     */
    private static $currentLang;

    /**
     * Translations
     * @var \Dflydev\DotAccessData\Data
     */
    private static $translations = null;

    /**
     * Instance of Lang
     * @var \Quantum\Libraries\Lang\Lang
     */
    private static $instance = null;

    /**
     * GetInstance
     * @return \Quantum\Libraries\Lang\Lang
     */
    public static function getInstance(int $langSegmentIndex = 1): ?Lang
    {
        if (self::$instance === null) {
            self::$instance = new self();

            if (!empty(route_prefix()) && $langSegmentIndex == 1) {
                $langSegmentIndex += 1;
            }

            $lang = Request::getSegment($langSegmentIndex);

            if (empty($lang) && !config()->get('lang_default')) {
                throw LangException::misconfiguredDefaultConfig();
            }

            if (empty($lang) || !in_array($lang, (array) config()->get('langs'))) {
                $lang = config()->get('lang_default');
            }

            self::$instance->setLang($lang);
        }

        return self::$instance;
    }

    /**
     * Loads translations
     * @throws \Quantum\Exceptions\LangException
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    public function load()
    {
        $fs = Di::get(FileSystem::class);

        $langDir = modules_dir() . DS . current_module() . DS . 'Resources' . DS . 'lang' . DS . $this->getLang();

        $files = $fs->glob($langDir . DS . "*.php");

        if (is_array($files) && !count($files)) {
            $langDir = base_dir() . DS . 'shared' . DS . 'Resources' . DS . 'lang' . DS . $this->getLang();

            $files = $fs->glob($langDir . DS . "*.php");

            if (is_array($files) && !count($files)) {
                throw LangException::translationsNotFound($this->getLang());
            }
        }

        self::$translations = new Data();

        foreach ($files as $file) {
            $fileName = $fs->fileName($file);

            $setup = new Setup();
            $setup->setPathPrefix('Resources' . DS . 'lang' . DS . $this->getLang());
            $setup->setFilename($fileName);
            $setup->setHierarchy(true);

            self::$translations->import([$fileName => Di::get(Loader::class)->setup($setup)->load()]);
        }
    }

    /**
     * Sets current language
     * @param string $lang
     * @return $this
     */
    public function setLang(string $lang): Lang
    {
        self::$currentLang = $lang;
        return $this;
    }

    /**
     * Gets the current language
     * @return string|null
     */
    public function getLang(): ?string
    {
        return self::$currentLang;
    }

    /**
     * Gets the whole translations of current language
     * @return \Dflydev\DotAccessData\Data
     */
    public function getTranslations(): ?Data
    {
        return self::$translations;
    }

    /**
     * Gets the translation by given key
     * @param string $key
     * @param string|array $params
     * @return string|null
     */
    public function getTranslation(string $key, $params = null): ?string
    {
        if (self::$translations && self::$translations->has($key)) {
            if (!is_null($params)) {
                return _message(self::$translations->get($key), $params);
            } else {
                return self::$translations->get($key);
            }
        } else {
            return $key;
        }
    }

    /**
     * Flushes loaded translations
     */
    public function flush()
    {
        self::$translations = null;
    }

}
