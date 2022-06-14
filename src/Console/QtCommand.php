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
 * @since 2.7.0
 */

namespace Quantum\Console;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Quantum\Environment\Environment;
use Quantum\Loader\Setup;

/**
 * Class QtCommand
 * @package Quantum\Console
 */
abstract class QtCommand extends Command implements CommandInterface
{

    /**
     * Console command name
     * @var string
     */
    protected $name;

    /**
     * Console command description.
     * @var string
     */
    protected $description;

    /**
     * Console command help text
     * @var string
     */
    protected $help;

    /**
     * Console command input arguments
     * @var array
     * @example ['name', 'type', 'description']
     */
    protected $args = [];

    /**
     * Console command options
     * @var array
     * @example ['name', 'shortcut', 'type', 'description', 'default']
     */
    protected $options = [];

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface;
     */
    protected $output;

    /**
     * QtCommand constructor.
     */
    public function __construct()
    {
        parent::__construct($this->name);

        $this->setDescription($this->description);

        $this->setHelp($this->help);
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setArguments();
        $this->setOptions();
    }

    /**
     * Executes the current command.
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->getName() != 'core:env') {
            Environment::getInstance()->load(new Setup('config', 'env'));
        }

        $this->input = $input;
        $this->output = $output;
        $this->exec();
    }

    /**
     * Returns the argument value for a given argument name.
     * @param string|null $key
     * @return mixed|string
     */
    protected function getArgument(string $key = null)
    {
        return $this->input->getArgument($key) ?? '';
    }

    /**
     * Returns the option value for a given option name.
     * @param string|null $key
     * @return mixed|string
     */
    protected function getOption(string $key = null)
    {
        return $this->input->getOption($key) ?? '';
    }

    /**
     * Outputs the string to console
     * @param string $string
     */
    public function output(string $string)
    {
        $this->output->writeln($string);
    }

    /**
     * Outputs the string to console as info
     * @param string $string
     */
    protected function info(string $string)
    {
        $this->output->writeln("<info>$string</info>");
    }

    /**
     * Outputs the string to console as comment
     * @param string $string
     */
    protected function comment(string $string)
    {
        $this->output->writeln("<comment>$string</comment>");
    }

    /**
     * Outputs the string to console as question
     * @param string $string
     */
    protected function question(string $string)
    {
        $this->output->writeln("<question>$string</question>");
    }

    /**
     * Outputs the string to console as error
     * @param string $string
     */
    protected function error(string $string)
    {
        $this->output->writeln("<error>$string</error>");
    }

    /**
     * Sets command arguments
     */
    private function setArguments()
    {
        foreach ($this->args as $arg) {
            switch ($arg[1]) {
                case 'required':
                    $this->addArgument($arg[0], InputArgument::REQUIRED, $arg[2]);
                    break;
                case 'optional':
                    $this->addArgument($arg[0], InputArgument::OPTIONAL, $arg[2]);
                    break;
                case 'array':
                    $this->addArgument($arg[0], InputArgument::IS_ARRAY, $arg[2]);
                    break;
            }
        }
    }

    /**
     * Sets command options
     */
    private function setOptions()
    {
        foreach ($this->options as $option) {
            switch ($option[2]) {
                case 'none':
                    $this->addOption($option[0], $option[1], InputOption::VALUE_NONE, $option[3]);
                    break;
                case 'required':
                    $this->addOption($option[0], $option[1], InputOption::VALUE_REQUIRED, $option[3], $option[4] ?? '');
                    break;
                case 'optional':
                    $this->addOption($option[0], $option[1], InputOption::VALUE_OPTIONAL, $option[3], $option[4] ?? '');
                    break;
                case 'array':
                    $this->addOption($option[0], $option[1], InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, $option[3], $option[4] ?? '');
                    break;
            }
        }
    }

}
