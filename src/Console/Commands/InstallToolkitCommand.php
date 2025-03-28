<?php

namespace Quantum\Console\Commands;

use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;
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
     * Command help text
     * @var string
     */
    protected $help = 'The command will install Toolkit and its assets into you project';
    
    /**
     * Command name to generate modules
     */
    const COMMAND_CREATE_MODULE = 'module:generate';

    /**
     * Executes the command
     * @throws ExceptionInterface
     */
    public function exec()
    {
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
     * @throws ExceptionInterface
     */
    protected function runExternalCommand($commandName, $arguments)
    {
        $command = $this->getApplication()->find($commandName);
        $command->run(new ArrayInput($arguments), new NullOutput);
    }
}