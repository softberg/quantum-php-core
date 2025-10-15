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

        if (!config()->has('lang')) {
            config()->import(new Setup('config', 'lang'));
        }

        $isEnabled = filter_var(config()->get('lang.enabled'), FILTER_VALIDATE_BOOLEAN);

        $langSegmentIndex = (int)config()->get('lang.url_segment');

        if (!empty(route_prefix()) && $langSegmentIndex == 1) {
            $langSegmentIndex++;
        }

        $lang = Request::getSegment($langSegmentIndex);

        if (empty($lang) || !in_array($lang, (array)config()->get('lang.supported'))) {
            $lang = config()->get('lang.default');
        }

        if (!$lang) {
            throw LangException::misconfiguredDefaultConfig();
        }

        $translator = new Translator($lang);

        return self::$instance = new Lang($lang, $isEnabled, $translator);
    }
}
