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

namespace Quantum\Exceptions;

/**
 * Class ViewException
 * @package Quantum\Exceptions
 */
class ViewException extends \Exception
{
    /**
     * @param string $name
     * @return \Quantum\Exceptions\ViewException
     */
    public static function directInstantiation(string $name): ViewException
    {
        return new static(t('direct_view_instance', $name), E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\ViewException
     */
    public static function noLayoutSet(): ViewException
    {
        return new static(t('layout_not_set'), E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ViewException
     */
    public static function fileNotFound(string $name): ViewException
    {
        return new static(t('view_file_not_found', $name), E_ERROR);
    }

    /**
     * @return \Quantum\Exceptions\ViewException
     */
    public static function missingTemplateEngineConfigs(): ViewException
    {
        return new static(t('template_engine_config_missing'), E_WARNING);
    }

}
