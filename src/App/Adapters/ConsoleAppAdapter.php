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

namespace Quantum\App\Adapters;

use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Quantum\Environment\Exceptions\EnvException;
use Quantum\Exceptions\StopExecutionException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Application;
use Quantum\App\Traits\ConsoleAppTrait;
use Quantum\App\Contracts\AppInterface;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use ReflectionException;

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/**
 * Class ConsoleAppAdapter
 * @package Quantum\App
 */
class ConsoleAppAdapter extends AppAdapter implements AppInterface
{

    use ConsoleAppTrait;

    /**
     * Console application name
     * @var string
     */
    private $name = 'Qt Console Application';

    /**
     * Console application version
     * @var string
     */
    private $version = '2.x';

    /**
     * @var Application
     */
    protected $application;

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
        $input = new ArgvInput();
        $output = new ConsoleOutput();

        try {
            $this->application = $this->createApplication($this->name, $this->version);

            if ($this->application->getName() !== 'core:env') {
                $this->loadEnvironment();
            }

            $this->loadConfig();
            $this->loadLanguage();

            $this->registerCoreCommands();
            $this->registerAppCommands();

            $this->setupErrorHandler();

            $this->validateCommand($input);

            $exitCode = $this->application->run($input, $output);

            stop(null, $exitCode);
        } catch (StopExecutionException $exception) {
            return $exception->getCode();
        }
    }
}