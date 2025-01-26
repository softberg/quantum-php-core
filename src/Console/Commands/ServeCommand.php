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

use Quantum\Di\Exceptions\DiException;
use Quantum\Console\QtCommand;

/**
 * Class ServeCommand
 * @package Quantum\Console
 */
class ServeCommand extends QtCommand
{

    /**
     * Platform Windows
     */
    const PLATFORM_WINDOWS = 'WINNT';

    /**
     * Platform Linux
     */
    const PLATFORM_LINUX = 'Linux';

    /**
     * Platform Mac
     */
    const PLATFORM_MAC = 'Darwin';

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'serve';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Serves the application on the PHP development server';

    /**
     * Default host
     * @var string
     */
    protected $defaultHost = '127.0.0.1';

    /**
     * Default port
     * @var int
     */
    protected $defaultPort = 8000;

    /**
     * The current port offset.
     * @var int
     */
    protected $portOffset = 0;

    /**
     * Command arguments
     * @var \string[][]
     */
    protected $options = [
        ['host', null, 'optional', 'Host', '127.0.0.1'],
        ['port', null, 'optional', 'Port', '8000'],
    ];

    /**
     * Executes the command
     * @throws DiException
     */
    public function exec()
    {
        if (!$this->portAvailable()) {
            $this->portOffset += 1;
            $this->exec();
        } else {
            if (!$this->openBrowserCommand()) {
                $this->info('Starting development server at: ' . $this->host() . ':' . $this->port());
                exec($this->runServerCommand(), $out);
            } else {
                $this->info('Starting development server at: ' . $this->host() . ':' . $this->port());
                exec($this->openBrowserCommand() . ' && ' . $this->runServerCommand(), $out);
            }
        }
    }

    /**
     * Starts the php development server
     * @return string
     */
    protected function runServerCommand(): string
    {
        return 'php -S ' . $this->host() . ':' . $this->port() . ' -t public';
    }

    /**
     * Tries to open the default browser
     * @return string|null
     */
    protected function openBrowserCommand(): ?string
    {
        $platformCommand = $this->detectPlatformCommand();

        if (!$platformCommand) {
            return null;
        }

        return $platformCommand . ' http://' . $this->host() . ':' . $this->port();
    }

    /**
     * Checks the available port
     * @return bool
     */
    protected function portAvailable(): bool
    {
        $connection = @fsockopen($this->host(), $this->port(), $errno, $err, 30);

        if (!is_resource($connection)) {
            return true;
        } else {
            fclose($connection);
            return false;
        }
    }

    /**
     * Detects the platform
     * @return string|null
     */
    protected function detectPlatformCommand(): ?string
    {
        switch (PHP_OS) {
            case self::PLATFORM_LINUX:
                return 'xdg-open';
            case self::PLATFORM_WINDOWS:
                return 'start';
            case self::PLATFORM_MAC:
                return 'open';
            default:
                return null;
        }
    }

    /**
     * Gets the host
     * @return mixed|string
     */
    protected function host()
    {
        return $this->getOption('host') ?: $this->defaultHost;
    }

    /**
     * Gets the port
     * @return int|mixed|string
     */
    protected function port()
    {
        $port = $this->getOption('port') ?: $this->defaultPort;
        return $port + $this->portOffset;
    }
}