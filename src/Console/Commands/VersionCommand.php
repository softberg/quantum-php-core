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
 * @since 2.9.5
 */

namespace Quantum\Console\Commands;

use Quantum\Environment\Exceptions\EnvException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Environment\Environment;
use Laminas\Text\Figlet\Figlet;
use Quantum\Console\QtCommand;
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

        $figlet->setFont(assets_dir() . DS . 'shared' . DS . 'fonts' . DS . 'figlet' . DS . 'slant.flf')->setSmushMode(Figlet::SM_SMUSH);

        $this->info($figlet->render('QUANTUM PHP ' . env('APP_VERSION')));

        $this->info('- - - Q U A N T U M   P H P   F R A M E W O R K  ' . env('APP_VERSION') . '  I N S T A L L E D - - -');
    }
}
