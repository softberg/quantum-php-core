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
 * @since 2.6.0
 */

namespace Quantum\Console\Commands;

use Quantum\Environment\Environment;
use Quantum\Console\QtCommand;
use Quantum\Loader\Setup;

/**
 * Class KeyGenerateCommand
 * @package Quantum\Console\Commands
 */
class KeyGenerateCommand extends QtCommand
{

    /**
     * Command name
     * @var string
     */
    protected $name = 'core:key';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Generates and stores the application key';

    /**
     * Command help text
     * @var string
     */
    protected $help = 'The command will generate APP_KEY and store in .env file';

    /**
     * The length of the key that will be generated (default 32)
     * @var array
     */
    protected $options = [
        ['length', 'l', 'required', 'Length of key', 32],
        ['yes', 'y', 'none', 'Answer of key generate']
    ];

    /**
     * Executes the command and stores the generated key to .env file
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\EnvException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function exec()
    {
        if (Environment::getInstance()->hasKey('APP_KEY') && env('APP_KEY') !== "") {
            if (!$this->getOption('yes')) {
                $message = "The operation will remove the existing key and will create new one. Continue?";

                if (!$this->confirm($message)) {
                    $this->info('Operation was canceled!');
                    return;
                }
            }
        }
        
        $key = $this->generateRandomKey();

        if ($key) {
            Environment::getInstance()->load(new Setup('config', 'env'))->updateRow('APP_KEY', $key);
        }

        $this->info('Application key successfully generated and stored.');
    }

    /**
     * Generates random string
     * @return string
     * @throws \Exception
     */
    private function generateRandomKey(): string
    {
        return base64_encode(random_bytes((int) $this->getOption('length')));
    }

}
