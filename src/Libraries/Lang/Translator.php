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
 * @since 2.9.9
 */

namespace Quantum\Libraries\Lang;

use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Dflydev\DotAccessData\Data;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class Translator
 * @package Quantum\Libraries\Lang
 */
class Translator
{

    protected $lang;
    
    /**
     * @var Data|null
     */
    private $translations = null;

    /**
     * @param string $lang
     */
    public function __construct(string $lang)
    {
        $this->lang = $lang;
    }

    /**
     * Load translation file
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws LangException
     * @throws ReflectionException
     */
    public function loadTranslations(): void
    {

        if ($this->translations !== null) {
            return;
        }

        $langDir = modules_dir() . DS . current_module() . DS . 'resources' . DS . 'lang' . DS . $this->lang;
        $files = fs()->glob($langDir . DS . "*.php");

        if (is_array($files) && !count($files)) {
            $langDir = base_dir() . DS . 'shared' . DS . 'resources' . DS . 'lang' . DS . $this->lang;
            $files = fs()->glob($langDir . DS . "*.php");

            if (is_array($files) && !count($files)) {
                throw LangException::translationsNotFound();
            }
        }

        $translations = [];

        foreach ($files as $file) {
            $fileName = fs()->fileName($file);

            $setup = new Setup();
            $setup->setPathPrefix('resources' . DS . 'lang' . DS . $this->lang);
            $setup->setFilename($fileName);
            $setup->setHierarchy(true);

            $translations[$fileName] = Di::get(Loader::class)->setup($setup)->load();
        }

        $this->translations = new Data();
        $this->translations->import($translations);
    }

    /**
     * Get translation by key
     * @param string $key
     * @param array|string|null $params
     * @return string
     */
    public function get(string $key, $params = null): string
    {
        if ($this->translations && $this->translations->has($key)) {
            $message = $this->translations->get($key);
            return $params ? _message($message, $params) : $message;
        }

        return $key;
    }

    /**
     * Reset translations
     */
    public function flush(): void
    {
        $this->translations = null;
    }
}