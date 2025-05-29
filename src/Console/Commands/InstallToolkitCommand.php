<?php

namespace Quantum\Console\Commands;

use Quantum\Environment\Exceptions\EnvException;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Quantum\Environment\Environment;
use Quantum\Console\QtCommand;

class InstallToolkitCommand extends QtCommand
{
    /**
     * Command name
     * @var string
     */
    protected $name = 'install:toolkit';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Installs toolkit';

    /**
     * Command arguments
     * @var array
     */
    protected $args = [
        ['username', 'required', 'The username for basic auth'],
        ['password', 'required', 'The password for basic auth'],
    ];

    /**
     * Command help text
     * @var string
     */
    protected $help = 'The command will install Toolkit and its assets into your project';
    
    /**
     * Command name to generate modules
     */
    const COMMAND_CREATE_MODULE = 'module:generate';

    /**
     * Executes the command
     * @throws ExceptionInterface
     * @throws EnvException
     */
    public function exec()
    {
        $name = $this->getArgument('username');
        
        $password = $this->getArgument('password');
        
        $env = Environment::getInstance();

        $env->setMutable(true);

        $env->updateRow('BASIC_AUTH_NAME', $name);
        
        $env->updateRow('BASIC_AUTH_PWD', $password);

        $this->runExternalCommand(self::COMMAND_CREATE_MODULE, [
            "module" => "Toolkit",
            "--yes" => true,
            "--template" => "Toolkit",
            "--with-assets" => true
        ]);

        $this->info('Toolkit installed successfully');
    }

    /**
     * Runs an external command
     * @param string $commandName
     * @param array $arguments
     * @throws ExceptionInterface
     */
    protected function runExternalCommand(string $commandName, array $arguments)
    {
        $command = $this->getApplication()->find($commandName);
        $command->run(new ArrayInput($arguments), new NullOutput);
    }
}