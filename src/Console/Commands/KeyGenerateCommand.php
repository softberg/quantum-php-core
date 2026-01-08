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
use Quantum\Environment\Environment;
use Quantum\Console\QtCommand;
use Exception;

/**
 * Class KeyGenerateCommand
 * @package Quantum\Console
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
     * Command options
     * @var array
     */
    protected $options = [
        ['length', 'l', 'required', 'Length of the key', 32],
        ['yes', 'y', 'none', 'Acceptance of the confirmation']
    ];

    /**
     * Executes the command and stores the generated key to .env file
     * @throws EnvException
     * @throws Exception
     */
    public function exec()
    {
        if (Environment::getInstance()->hasKey('APP_KEY') && env('APP_KEY') !== "" && !$this->getOption('yes')) {
            if (!$this->confirm("The operation will remove the existing key and will create new one. Continue?")) {
                $this->info('Operation was canceled!');
                return;
            }
        }

        Environment::getInstance()
            ->setMutable(true)
            ->updateRow('APP_KEY', $this->generateRandomKey());

        $this->info('Application key successfully generated and stored.');
    }

    /**
     * Generates random string
     * @return string
     * @throws Exception
     */
    private function generateRandomKey(): string
    {
        return bin2hex(random_bytes((int)$this->getOption('length')));
    }
}
