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

namespace Quantum\Libraries\Lang\Factories;

use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Lang\Translator;
use Quantum\Libraries\Lang\Lang;
use Quantum\Http\Request;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class LangFactory
 * @package Quantum\Libraries\Lang
 */
class LangFactory
{

    /**
     * @var Lang|null Cached Lang instance
     */
    private static $instance = null;

    /**
     * @return Lang
     * @throws ConfigException
     * @throws LangException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(): Lang
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        list($isEnabled, $supported, $default) = self::loadLangConfig();

        $lang = self::detectLanguage($supported, $default);

        $translator = new Translator($lang);

        return self::$instance = new Lang($lang, $isEnabled, $translator);

    }

    /**
     * @return array
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private static function loadLangConfig(): array
    {
        if (!config()->has('lang')) {
            config()->import(new Setup('config', 'lang'));
        }

        return [
            filter_var(config()->get('lang.enabled'), FILTER_VALIDATE_BOOLEAN),
            (array)config()->get('lang.supported'),
            config()->get('lang.default'),
        ];
    }

    /**
     * @param array $supported
     * @param string|null $default
     * @return string
     * @throws LangException
     */
    private static function detectLanguage(array $supported, ?string $default): string
    {
        $lang = self::getLangFromQuery($supported);

        if (in_array($lang, [null, '', '0'], true)) {
            $lang = self::getLangFromUrlSegment($supported);
        }

        if (in_array($lang, [null, '', '0'], true)) {
            $lang = self::getLangFromHeader($supported);
        }

        if (in_array($lang, [null, '', '0'], true)) {
            $lang = $default;
        }

        if (!$lang) {
            throw LangException::misconfiguredDefaultConfig();
        }

        return $lang;
    }

    /**
     * @param array $supported
     * @return string|null
     */
    private static function getLangFromQuery(array $supported): ?string
    {
        $queryLang = Request::getQueryParam('lang');

        return $queryLang && in_array($queryLang, $supported) ? $queryLang : null;
    }

    /**
     * @param array $supported
     * @return string|null
     */
    private static function getLangFromUrlSegment(array $supported): ?string
    {
        $segmentIndex = (int)config()->get('lang.url_segment');

        if (!in_array(route_prefix(), [null, '', '0'], true) && $segmentIndex === 1) {
            $segmentIndex++;
        }

        $segmentLang = Request::getSegment($segmentIndex);

        return $segmentLang && in_array($segmentLang, $supported) ? $segmentLang : null;
    }

    /**
     * @param array $supported
     * @return string|null
     */
    private static function getLangFromHeader(array $supported): ?string
    {
        $acceptedLang = server()->acceptedLang();

        return $acceptedLang && in_array($acceptedLang, $supported) ? $acceptedLang : null;
    }
}