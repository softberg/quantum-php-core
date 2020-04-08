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

namespace Quantum\Dumper;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Dumper\CliDumper;

/**
 * Dumper class
 * 
 * @package Quantum
 * @subpackage Libraries.Dumper
 * @category Libraries
 */
class Dumper
{

    /**
     * Dumper Style
     * 
     * @var array 
     */
    public static $dumperStyle = [
        'default' => 'background-color:#FFFFFF; color:#FF8400; line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
        'public' => 'color:#222222',
        'protected' => 'color:#222222',
        'private' => 'color:#222222'
    ];

    /**
     * Dump
     * 
     * @param mixed $var
     * @param bool $die
     */
    public function dump($var, $die)
    {
        if (get_config('debug') && !$die) {
            $debugOutput = session()->get('_qt_debug_output') ?? [];
            array_push($debugOutput, $var);
            session()->set('_qt_debug_output', $debugOutput);
        } else {
            $cloner = new VarCloner();
            $dumper = PHP_SAPI === 'cli' ? new CliDumper() : new HtmlDumper();
            $dumper->setStyles(self::$dumperStyle);
            $dumper->dump($cloner->cloneVar($var));
        }
    }

}
