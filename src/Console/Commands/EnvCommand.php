<?php

namespace Quantum\Console\Commands;

use Quantum\Console\Qt_Command;

class EnvCommand extends Qt_Command
{
    protected $name = 'core:env';

    protected $description = 'Copying env file from .env.example';

    protected $help = 'Copying env file from .env.example';


    public function exec()
    {
        if(file_exists('.env.example')) {
            copy('.env.example', '.env');
            $this->info('.env is copied');
        } else {
            $this->error('.env.example file not found');
        }
    }
}