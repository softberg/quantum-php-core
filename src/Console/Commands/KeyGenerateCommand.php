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
 * @since 1.7.0
 */

namespace Quantum\Console\Commands;

use Quantum\Console\Qt_Command;
use Quantum\Libraries\Environment\Environment;

/**
 * Class KeyGenerateCommand
 * @package Quantum\Console\Commands
 */
class KeyGenerateCommand extends Qt_Command
{
    /**
     * Command name
     *
     * @var string
     */
    protected $name = 'core:key-generate';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Generates and stores the application key';

    /**
     * Command help text
     *
     * @var string
     */
    protected $help = 'The command will generate APP_KEY and store in .env file';

    /**
     * Command argument
     *
     * The length of the key that will be generated (default 32)
     *
     * @var array
     */
    protected $options = [
        ['length', 'l', 'required', 'Length of key', 32]
    ];

    /**
     * Executes the command and stores the generated key to .env file
     *
     * @return mixed|void
     * @throws \Exception
     */
    public function exec()
    {
        $key = $this->generateRandomKey();

        if ($key) {
            Environment::updateRow('APP_KEY', $key);
        }

        $this->info('Application key successfully generated and stored.');
    }

    /**
     * Generates random string
     *
     * @return string
     * @throws \Exception
     */
    private function generateRandomKey()
    {
        return base64_encode(random_bytes($this->getOption('length')));
    }

}