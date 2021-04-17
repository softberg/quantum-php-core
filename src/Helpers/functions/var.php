<?php

use Symfony\Component\VarDumper\Cloner\AbstractCloner;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarExporter\VarExporter;

if (!function_exists('out')) {

    /**
     * Outputs the dump of teh variable
     * @param mixed $var
     * @param bool $die
     * @throws ErrorException
     */
    function out($var, $die = false)
    {

        $dumperStyle = [
            'default' => 'background-color:#FFFFFF; color:#FF8400; line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
            'public' => 'color:#222222',
            'protected' => 'color:#222222',
            'private' => 'color:#222222'
        ];

        if (config()->get('debug') && !$die) {
            $debugOutput = (array)session()->get('_qt_debug_output') ?? [];
            array_push($debugOutput, $var);
            session()->set('_qt_debug_output', $debugOutput);
        } else {
            $cloner = new VarCloner();
            $dumper = PHP_SAPI === 'cli' ? new CliDumper() : new HtmlDumper();
            $dumper->setStyles($dumperStyle);
            $dumper->dump($cloner->cloneVar($var));
        }

        if ($die) {
            die;
        }
    }

    if (!function_exists('export')) {

        /**
         * Exports the variable
         * @param mixed $var
         * @return string
         * @throws \Symfony\Component\VarExporter\Exception\ExceptionInterface
         */
        function export($var)
        {
            return VarExporter::export($var);
        }
    }

}