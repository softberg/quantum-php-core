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
 * @since 2.5.0
 */

namespace Quantum\Logger;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Quantum\Contracts\ReportableInterface;
use Quantum\Debugger\Debugger;

/**
 * Class MessageLogger
 * @package Quantum\Logger
 */
class MessageLogger implements ReportableInterface
{

    /**
     * Dumper styles
     * @var string[]
     */
    private $dumperStyle = [
        'default' => 'background-color:#FFFFFF; color:#FF8400; line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
        'public' => 'color:#222222',
        'protected' => 'color:#222222',
        'private' => 'color:#222222'
    ];

    /**
     * @inheritDoc
     */
    public function report($level, $message, array $context = [])
    {
        if (filter_var(config()->get('debug'), FILTER_VALIDATE_BOOLEAN)) {
            Debugger::addToStore(Debugger::MESSAGES, $level, $message);
        } else {
            $cloner = new VarCloner();
            $dumper = PHP_SAPI === 'cli' ? new CliDumper() : new HtmlDumper();
            $dumper->setStyles($this->dumperStyle);
            $dumper->dump($cloner->cloneVar($message));
        }
    }

}