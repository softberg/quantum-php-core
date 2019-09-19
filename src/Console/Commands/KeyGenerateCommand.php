<?php

namespace Quantum\Console\Commands;

use Quantum\Console\Qt_Command;
use Quantum\Libraries\Environment\Environment;

class KeyGenerateCommand extends Qt_Command
{
    protected $name = 'core:key-generate';

    protected $description = 'Generate APP_KEY';

    protected $help = 'Generate APP_KEY';

    protected $options = [
        ['length', 'l', 'required', 'Length of key', 32]
    ];


    public function exec()
    {
        $key = $this->generateRandomKey();

        if($key) {
            Environment::updateRow('APP_KEY', $key);
        }

        $this->info('Application key set successfully.');
    }

    private function generateRandomKey()
    {
        return base64_encode(random_bytes($this->getOption('length')));
    }

}