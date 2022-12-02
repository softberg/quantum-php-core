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

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Console\QtCommand;
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
     * The default action for all confirmations
     * @var array
     */
    protected $options = [
        ['yes', 'y', 'none', 'Answer of overwrite .env file']
    ];

    /**
     * Executes the command and creates new .env file
     */
    public function exec()
    {
        $fs = Di::get(FileSystem::class);

        if ($fs->exists('.env.example')) {
            if (!$this->getOption('yes')) {
                if ($fs->exists('.env')) {
                    $message = "The operation will overwrite values of the existing .env and will create new one from .env.example. Continue?";

                    if (!$this->confirm($message)) {
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
