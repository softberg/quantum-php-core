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
 * @since 3.0.0
 */

namespace Quantum\Libraries\Lang;

use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Dflydev\DotAccessData\Data;
use ReflectionException;

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
     * Load translation files
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

        $this->translations = new Data();
        $loaded = false;

        $sharedDir = base_dir() . DS . 'shared' . DS . 'resources' . DS . 'lang' . DS . $this->lang;
        $sharedFiles = fs()->glob($sharedDir . DS . '*.php');

        if (is_array($sharedFiles) && count($sharedFiles)) {
            $this->loadFiles($sharedFiles);
            $loaded = true;
        }

        $moduleDir = modules_dir() . DS . current_module() . DS . 'resources' . DS . 'lang' . DS . $this->lang;
        $moduleFiles = fs()->glob($moduleDir . DS . '*.php');

        if (is_array($moduleFiles) && count($moduleFiles)) {
            $this->loadFiles($moduleFiles);
            $loaded = true;
        }

        if (!$loaded) {
            throw LangException::translationsNotFound();
        }
    }

    /**
     * Load translations
     * @param array $files
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private function loadFiles(array $files): void
    {
        foreach ($files as $file) {
            $fileName = fs()->fileName($file);

            $this->translations->import([
                $fileName => fs()->require($file),
            ]);
        }
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
