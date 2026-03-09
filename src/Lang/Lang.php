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

namespace Quantum\Lang;

use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use ReflectionException;

/**
 * Class Lang
 * @package Quantum\Lang
 */
class Lang
{
    private ?string $currentLang = null;

    private Translator $translator;

    private bool $isEnabled;

    public function __construct(string $lang, bool $isEnabled, Translator $translator)
    {
        $this->isEnabled = $isEnabled;
        $this->translator = $translator;
        $this->setLang($lang);
    }

    /**
     * Set the current language
     * @return $this
     */
    public function setLang(string $lang): self
    {
        $this->currentLang = $lang;
        return $this;
    }

    /**
     * Get current language
     */
    public function getLang(): ?string
    {
        return $this->currentLang;
    }

    /**
     * Is multilang enabled
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Load translations
     * @throws Exceptions\LangException
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public function load(): void
    {
        $this->translator->loadTranslations();
    }

    /**
     * Get translation by key
     * @param $params
     */
    public function getTranslation(string $key, $params = null): ?string
    {
        return $this->translator->get($key, $params);
    }

    /**
     * Flush loaded translations
     */
    public function flush(): void
    {
        $this->translator->flush();
    }
}
