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
 * @since 2.0.0
 */

namespace Quantum\Console\Commands;

use Quantum\Environment\Environment;
use Quantum\Console\QtCommand;
use Quantum\Loader\Loader;
use Figlet\Figlet;
use Quantum\Di\Di;

/**
 * Class WelcomeCommand
 * @package Quantum\Console\Commands
 */
class WelcomeCommand extends QtCommand
{

    /**
     * Command name
     * @var string
     */
    protected $name = 'core:welcome';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Installation greetings';

    /**
     * Command help text
     * @var string
     */
    protected $help = 'Printing greetings into the terminal';

    /**
     * Executes the command and prints greetings into the terminal
     * @return void
     */
    public function exec()
    {
        Di::loadDefinitions();

        $loader = Di::get(Loader::class);

        Environment::getInstance()->load($loader);

        $figlet = new Figlet();

        $figlet->setFont('slant.flf')->setSmushMode(Figlet::SM_SMUSH);

        $this->info($figlet->render('QUANTUM PHP ' . env('APP_VERSION')));

        $this->info('- - - Q U A N T U M   P H P   F R A M E W O R K  ' . env('APP_VERSION') . '  I N S T A L L E D - - -');
    }

}
