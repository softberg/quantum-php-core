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
 * @since 3.0.0
 */

namespace Quantum\Console;

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Quantum\Console\Contracts\CommandInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Class QtCommand
 * @package Quantum\Console
 */
abstract class QtCommand extends Command implements CommandInterface
{
    /**
     * Console command name
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * Console command description.
     * @var string|null
     */
    protected ?string $description = null;

    /**
     * Console command help text
     * @var string|null
     */
    protected ?string $help = null;

    /**
     * Console command input arguments
     * @var array
     * @example ['name', 'type', 'description']
     */
    protected array $args = [];

    /**
     * Console command options
     * @var array
     * @example ['name', 'shortcut', 'type', 'description', 'default']
     */
    protected array $options = [];

    /**
     * @var InputInterface|null
     */
    protected ?InputInterface $input = null;

    /**
     * @var OutputInterface|null
     */
    protected ?OutputInterface $output = null;

    /**
     * QtCommand constructor.
     */
    public function __construct()
    {
        parent::__construct($this->name);

        if ($this->description !== null) {
            $this->setDescription($this->description);
        }

        if ($this->help !== null) {
            $this->setHelp($this->help);
        }
    }

    /**
     * Returns the argument value for a given argument name.
     * @param string|null $key
     * @return mixed|string
     */
    public function getArgument(?string $key = null)
    {
        return $this->input->getArgument($key) ?? '';
    }

    /**
     * Returns the option value for a given option name.
     * @param string|null $key
     * @return mixed|string
     */
    public function getOption(?string $key = null)
    {
        return $this->input->getOption($key) ?? '';
    }

    /**
     * Outputs the string to console
     * @param string $message
     */
    public function output(string $message): void
    {
        $this->output->writeln($message);
    }

    /**
     * Outputs the string to the console as info
     * @param string $message
     */
    public function info(string $message): void
    {
        $this->output->writeln("<info>$message</info>");
    }

    /**
     * Outputs the string to console as comment
     * @param string $message
     */
    public function comment(string $message): void
    {
        $this->output->writeln("<comment>$message</comment>");
    }

    /**
     * Outputs the string to console as question
     * @param string $message
     */
    public function question(string $message): void
    {
        $this->output->writeln("<question>$message</question>");
    }

    /**
     * Asks confirmation
     * @param string $message
     * @return bool
     */
    public function confirm(string $message): bool
    {
        $helper = $this->getHelper('question');

        $question = new ConfirmationQuestion($message . ' [y/N] ', false);

        return (bool) $helper->ask($this->input, $this->output, $question);
    }

    /**
     * Outputs the string to the console as an error
     * @param string $message
     */
    public function error(string $message): void
    {
        $this->output->writeln("<error>$message</error>");
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
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->exec();

        return Command::SUCCESS;
    }

    /**
     * Sets command arguments
     */
    private function setArguments(): void
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
    private function setOptions(): void
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
