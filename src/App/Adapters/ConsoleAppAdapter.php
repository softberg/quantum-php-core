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
 * @since 2.9.9
 */

namespace Quantum\App\Adapters;

use Quantum\Libraries\Lang\Exceptions\LangException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Quantum\App\Exceptions\StopExecutionException;
use Quantum\Environment\Exceptions\EnvException;
use Symfony\Component\Console\Input\ArgvInput;
use Quantum\Config\Exceptions\ConfigException;
use Symfony\Component\Console\Application;
use Quantum\App\Exceptions\BaseException;
use Quantum\App\Traits\ConsoleAppTrait;
use Quantum\Di\Exceptions\DiException;
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

    /**
     * @var ArgvInput
     */
    protected $input;

    /**
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * @var Application
     */
    protected $application;


    public function __construct()
    {
        parent::__construct();

        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();

        $commandName = $this->input->getFirstArgument();

        if ($commandName !== 'core:env') {
            $this->loadEnvironment();
            $this->loadAppConfig();
        }
    }

    /**
     * @return int|null
     * @throws DiException
     * @throws EnvException
     * @throws BaseException
     * @throws ConfigException
     * @throws LangException
     * @throws ReflectionException
     */
    public function start(): ?int
    {
        try {
            $this->application = $this->createApplication(
                config()->get('app.name', 'UNKNOWN'),
                config()->get('app.version', 'UNKNOWN')
            );

            $this->loadLanguage();

            $this->registerCoreCommands();
            $this->registerAppCommands();

            $this->setupErrorHandler();

            $this->validateCommand();

            $exitCode = $this->application->run($this->input, $this->output);

            stop(null, $exitCode);
        } catch (StopExecutionException $exception) {
            return $exception->getCode();
        }
    }
}