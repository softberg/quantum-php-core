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
 * @since 2.9.8
 */

namespace Quantum\Libraries\Lang;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Dflydev\DotAccessData\Data;
use Quantum\Loader\Loader;
use Quantum\Http\Request;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Language class
 * @package Quantum\Libraries\Lang
 */
class Lang
{

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
     * @var Data
     */
    private static $translations = null;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * Instance of Lang
     * @var Lang
     */
    private static $instance = null;

    /**
     * @throws LangException
     */
    private function __construct()
    {
        $this->isEnabled = filter_var(config()->get('multilang'), FILTER_VALIDATE_BOOLEAN);

        $langSegmentIndex = (int)config()->get(Lang::LANG_SEGMENT);

        if (!empty(route_prefix()) && $langSegmentIndex == 1) {
            $langSegmentIndex++;
        }

        $lang = Request::getSegment($langSegmentIndex);

        if (empty($lang) && !config()->get('lang_default')) {
            throw LangException::misconfiguredDefaultConfig();
        }

        if (empty($lang) || !in_array($lang, (array)config()->get('langs'))) {
            $lang = config()->get('lang_default');
        }

        $this->setLang($lang);
    }

    /**
     * GetInstance
     * @return Lang
     */
    public static function getInstance(): Lang
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     *Loads translations
     * @throws DiException
     * @throws LangException
     * @throws ReflectionException
     * @throws BaseException
     */
    public function load()
    {
        $fs = FileSystemFactory::get();

        $langDir = modules_dir() . DS . current_module() . DS . 'resources' . DS . 'lang' . DS . $this->getLang();

        $files = $fs->glob($langDir . DS . "*.php");

        if (is_array($files) && !count($files)) {
            $langDir = base_dir() . DS . 'shared' . DS . 'resources' . DS . 'lang' . DS . $this->getLang();

            $files = $fs->glob($langDir . DS . "*.php");

            if (is_array($files) && !count($files)) {
                throw LangException::translationsNotFound($this->getLang());
            }
        }

        $translations = [];

        foreach ($files as $file) {
            $fileName = $fs->fileName($file);

            $setup = new Setup();
            $setup->setPathPrefix('resources' . DS . 'lang' . DS . $this->getLang());
            $setup->setFilename($fileName);
            $setup->setHierarchy(true);

            $translations[$fileName] = Di::get(Loader::class)->setup($setup)->load();
        }

        $this->setTranslations($translations);
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
     * Sets translations manually (for testing purposes)
     * @param array $translations
     */
    public function setTranslations(array $translations)
    {
        if (self::$translations === null) {
            self::$translations = new Data();
        }

        self::$translations->import($translations);
    }

    /**
     * Gets the whole translations of current language
     * @return Data
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
            $message = self::$translations->get($key);
            return $params ? _message($message, $params) : $message;
        }

        return $key;
    }

    /**
     * Flushes loaded translations
     */
    public function flush()
    {
        self::$translations = null;
    }
}