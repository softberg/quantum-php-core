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

namespace Quantum\Console\Commands;

use Quantum\Environment\Exceptions\EnvException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Environment\Environment;
use Quantum\Console\QtCommand;
use Povils\Figlet\Figlet;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class VersionCommand
 * @package Quantum\Console
 */
class VersionCommand extends QtCommand
{
    /**
     * Command name
     * @var string
     */
    protected $name = 'core:version';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Core version';

    /**
     * Command help text
     * @var string
     */
    protected $help = 'Printing the current version of the framework into the terminal';

    /**
     * Executes the command and prints greetings into the terminal
     * @throws DiException
     * @throws EnvException
     * @throws BaseException
     * @throws ReflectionException
     */
    public function exec()
    {
        Environment::getInstance()->load(new Setup('config', 'env'));

        $figlet = new Figlet();

        $renderedFiglet = $figlet
            ->setFontDir(assets_dir() . DS . 'shared' . DS . 'fonts' . DS . 'figlet' . DS)
            ->setFont('slant')
            ->render('QUANTUM PHP ' . env('APP_VERSION'));

        $this->info($renderedFiglet);

        $this->info('- - - Q U A N T U M   P H P   F R A M E W O R K  ' . env('APP_VERSION') . '  I N S T A L L E D - - -');
    }
}
