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

namespace Quantum\Console\Commands;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Environment\Environment;
use Quantum\Exceptions\EnvException;
use Quantum\Console\QtCommand;
use Quantum\Loader\Setup;
use Quantum\Di\Di;

/**
 * Class EnvCommand
 * @package Quantum\Console\Commands
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
        ['yes', 'y', 'none', 'Acceptance of the confirmation']
    ];

    /**
     * Executes the command and creates new .env file
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     * @throws EnvException
     */
    public function exec()
    {
        $fs = Di::get(FileSystem::class);

        if ($fs->exists('.env.example')) {
            if (!$this->getOption('yes')) {
                if ($fs->exists('.env')) {
                    if (!$this->confirm("The operation will overwrite values of the existing .env and will create new one from .env.example. Continue?")) {
                        $this->info('Operation was canceled!');
                        return;
                    }
                }
            }

            copy('.env.example', '.env');

            $this->info('.env is successfully generated');
        } else {
            $this->error('.env.example file not found');
        }
    }
}
