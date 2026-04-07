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

namespace Quantum\App\Adapters;

use Quantum\App\Stages\RegisterCoreDependenciesStage;
use Symfony\Component\Console\Output\ConsoleOutput;
use Quantum\App\Exceptions\StopExecutionException;
use Symfony\Component\Console\Input\ArgvInput;
use Quantum\App\Stages\SetupErrorHandlerStage;
use Quantum\App\Stages\LoadEnvironmentStage;
use Symfony\Component\Console\Application;
use Quantum\App\Stages\LoadAppConfigStage;
use Quantum\App\Stages\LoadLanguageStage;
use Quantum\App\Stages\LoadHelpersStage;
use Quantum\App\Traits\ConsoleAppTrait;
use Quantum\App\Enums\AppType;
use Quantum\App\BootPipeline;
use ReflectionException;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * Class ConsoleAppAdapter
 * @package Quantum\App
 */
class ConsoleAppAdapter extends AppAdapter
{
    use ConsoleAppTrait;

    protected ArgvInput $input;

    protected ConsoleOutput $output;

    protected Application $application;

    public function __construct()
    {
        parent::__construct(AppType::CONSOLE);

        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();

        $commandName = $this->input->getFirstArgument();

        $stages = [
            new RegisterCoreDependenciesStage(),
            new LoadHelpersStage(),
        ];

        if ($commandName !== 'core:env') {
            $stages[] = new LoadEnvironmentStage();
            $stages[] = new LoadAppConfigStage();
        }

        $pipeline = new BootPipeline($stages);
        $pipeline->run($this->context);

        $this->application = $this->createApplication(
            config()->get('app.name', 'UNKNOWN'),
            config()->get('app.version', 'UNKNOWN')
        );
    }

    /**
     * @throws ReflectionException
     */
    public function start(): ?int
    {
        try {
            (new LoadLanguageStage())->process($this->context);

            $this->registerCoreCommands();
            $this->registerAppCommands();

            (new SetupErrorHandlerStage())->process($this->context);

            $this->validateCommand();

            $exitCode = $this->application->run($this->input, $this->output);

            stop(null, $exitCode);
        } catch (StopExecutionException $exception) {
            return $exception->getCode();
        }
    }
}
