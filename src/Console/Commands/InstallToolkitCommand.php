<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Console\Commands;

use Symfony\Component\Console\Exception\ExceptionInterface;
use Quantum\Environment\Exceptions\EnvException;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Quantum\Environment\Environment;
use Quantum\Console\QtCommand;
use RuntimeException;

/**
 * Class InstallToolkitCommand
 * @package Quantum\Console
 */
class InstallToolkitCommand extends QtCommand
{
    /**
     * Command name
     */
    protected ?string $name = 'install:toolkit';

    /**
     * Command description
     */
    protected ?string $description = 'Installs toolkit';

    /**
     * Command arguments
     */
    protected array $args = [
        ['username', 'required', 'The username for basic auth'],
        ['password', 'required', 'The password for basic auth'],
    ];

    /**
     * Command help text
     */
    protected ?string $help = 'The command will install Toolkit and its assets into your project';

    /**
     * Command name to generate modules
     */
    public const COMMAND_CREATE_MODULE = 'module:generate';

    /**
     * Executes the command
     * @throws ExceptionInterface
     * @throws EnvException
     */
    public function exec(): void
    {
        $name = $this->getArgument('username');

        $password = $this->getArgument('password');

        $env = Environment::getInstance();

        $env->setMutable(true);

        $env->updateRow('BASIC_AUTH_NAME', $name);

        $env->updateRow('BASIC_AUTH_PWD', $password);

        $this->runExternalCommand(self::COMMAND_CREATE_MODULE, [
            'module' => 'Toolkit',
            '--yes' => true,
            '--template' => 'Toolkit',
            '--with-assets' => true,
        ]);

        $this->info('Toolkit installed successfully');
    }

    /**
     * Runs an external command
     * @param array<string, mixed> $arguments
     * @throws ExceptionInterface
     */
    protected function runExternalCommand(string $commandName, array $arguments): void
    {
        $application = $this->getApplication();

        if ($application === null) {
            throw new RuntimeException('Application is not set.');
        }

        $command = $application->find($commandName);
        $command->run(new ArrayInput($arguments), new NullOutput());
    }
}
