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

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Console\QtCommand;
use ReflectionException;
use Quantum\App\App;

/**
 * Class EnvCommand
 * @package Quantum\Console
 */
class EnvCommand extends QtCommand
{
    /**
     * Command name
     * @var string
     */
    protected $name = 'core:env';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Generates new .env file';

    /**
     * Command help text
     * @var string
     */
    protected $help = 'The command will generate new .env file from .env.example';

    /**
     * Command options
     * @var array
     */
    protected $options = [
        ['yes', 'y', 'none', 'Acceptance of the confirmation'],
    ];

    /**
     * Executes the command and creates new .env file
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public function exec()
    {
        $fs = FileSystemFactory::get();

        if (!$fs->exists(App::getBaseDir() . DS . '.env.example')) {
            $this->error('.env.example file not found');
        }

        if ($fs->exists('.env') && !$this->getOption('yes') && !$this->confirm('The operation will overwrite values of the existing .env and will create new one from .env.example. Continue?')) {
            $this->info('Operation was canceled!');
            return;
        }

        $fs->copy(
            App::getBaseDir() . DS . '.env.example',
            App::getBaseDir() . DS . '.env'
        );

        $this->info('.env is successfully generated');
    }
}
