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

use Quantum\Console\QtCommand;

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
     * Executes the command and creates new .env file
     */
    public function exec()
    {
        if (file_exists('.env.example')) {
            copy('.env.example', '.env');
            $this->info('.env is successfully generated');
        } else {
            $this->error('.env.example file not found');
        }
    }

}
