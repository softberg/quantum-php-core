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

namespace Quantum\Console\Commands;

use Quantum\Console\QtCommand;
use RuntimeException;

/**
 * Class ServeCommand
 * @package Quantum\Console
 */
class ServeCommand extends QtCommand
{
    /**
     * Platform Windows
     */
    public const PLATFORM_WINDOWS = 'WINNT';

    /**
     * Platform Linux
     */
    public const PLATFORM_LINUX = 'Linux';

    /**
     * Platform Mac
     */
    public const PLATFORM_MAC = 'Darwin';

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
     * Max ports to scan
     * @var int
     */
    protected $maxPortScan = 50;

    /**
     * Command arguments
     * @var array<int, list<string|null>>
     */
    protected $options = [
        ['host', null, 'optional', 'Host', '127.0.0.1'],
        ['port', null, 'optional', 'Port', '8000'],
        ['open', 'o', 'none', 'Open browser'],
    ];

    /**
     * Executes the command
     */
    public function exec()
    {
        $endpoint = $this->resolveEndpoint();

        $this->info("Starting development server at: {$endpoint['url']}");

        $process = $this->startPhpServer($endpoint['host'], $endpoint['port']);
        $this->waitUntilServerIsReady($endpoint['host'], $endpoint['port'], $process);

        if ($this->shouldOpenBrowser()) {
            $this->openBrowser($endpoint['url']);
        }

        $this->waitForProcess($process);
    }

    /**
     * Resolves the endpoint
     * @return array
     */
    protected function resolveEndpoint(): array
    {
        $host = $this->host();
        $port = $this->findAvailablePort($host, $this->port());

        return [
            'host' => $host,
            'port' => $port,
            'url' => "http://{$host}:{$port}",
        ];
    }

    /**
     * Starts the php development server
     * @param string $host
     * @param int $port
     * @return resource
     */
    protected function startPhpServer(string $host, int $port)
    {
        $cmd = [
            PHP_BINARY,
            '-S',
            "{$host}:{$port}",
            '-t',
            'public',
        ];

        $descriptors = [
            0 => STDIN,
            1 => STDOUT,
            2 => STDERR,
        ];

        $process = proc_open($cmd, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new RuntimeException('Unable to start PHP development server.');
        }

        return $process;
    }

    /**
     * Wait until the PHP server is ready to accept connections.
     * @param string $host
     * @param int $port
     * @param $process
     * @return void
     */
    protected function waitUntilServerIsReady(string $host, int $port, $process): void
    {
        $start = time();

        while (true) {
            $status = proc_get_status($process);
            if ($status === false || !$status['running']) {
                throw new RuntimeException('PHP server process died unexpectedly.');
            }

            $fp = @fsockopen($host, $port, $e, $s, 0.5);
            if ($fp) {
                fclose($fp);
                return;
            }

            if (time() - $start > 10) {
                throw new RuntimeException('Server failed to start within 10 seconds.');
            }

            usleep(200_000);
        }
    }

    /**
     * Wait until the PHP server is ready to accept connections.
     * @param $process
     * @return void
     */
    protected function waitForProcess($process): void
    {
        try {
            while (true) {
                $status = proc_get_status($process);
                if ($status === false || !$status['running']) {
                    break;
                }

                usleep(200_000);
            }
        } finally {
            proc_close($process);
        }
    }

    /**
     * Block until the PHP server process exits.
     * @param string $host
     * @param int $startPort
     * @return int
     */
    protected function findAvailablePort(string $host, int $startPort): int
    {
        for ($i = 0; $i < $this->maxPortScan; $i++) {
            $port = $startPort + $i;
            if ($this->canBind($host, $port)) {
                return $port;
            }
        }

        throw new RuntimeException("No available ports found starting from {$startPort}");
    }

    /**
     * Check whether the given host and port can be bound.
     * @param string $host
     * @param int $port
     * @return bool
     */
    protected function canBind(string $host, int $port): bool
    {
        $socket = @stream_socket_server(
            "tcp://{$host}:{$port}",
            $errno,
            $errstr,
            STREAM_SERVER_BIND | STREAM_SERVER_LISTEN
        );

        if ($socket === false) {
            return false;
        }

        fclose($socket);
        return true;
    }

    /**
     * Determine whether the browser should be opened.
     * @return bool
     */
    protected function shouldOpenBrowser(): bool
    {
        return $this->getOption('open');
    }

    /**
     * Open the default system browser for the given URL.
     * @param string $url
     * @return void
     */
    protected function openBrowser(string $url): void
    {
        $cmd = $this->browserCommand($url);
        if (!$cmd) {
            return;
        }

        $descriptors = [
            0 => STDIN,
            1 => STDOUT,
            2 => STDERR,
        ];

        $proc = proc_open($cmd, $descriptors, $pipes);
        if (is_resource($proc)) {
            proc_close($proc);
        }
    }

    /**
     * Resolve the platform-specific command used to open a URL.
     * @param string $url
     * @return string[]|null
     */
    protected function browserCommand(string $url): ?array
    {
        switch (PHP_OS) {
            case self::PLATFORM_WINDOWS:
                return ['explorer.exe', $url];
            case self::PLATFORM_LINUX:
                return ['xdg-open', $url];
            case self::PLATFORM_MAC:
                return ['open', $url];
            default:
                return null;
        }
    }

    /**
     * Resolve the platform-specific command used to open a URL.
     * @return string
     */
    protected function host(): string
    {
        return (string) ($this->getOption('host') ?: $this->defaultHost);
    }

    /**
     * Get and validate the port option.
     * @return int
     */
    protected function port(): int
    {
        $port = (int) ($this->getOption('port') ?: $this->defaultPort);

        if ($port < 1 || $port > 65535) {
            throw new RuntimeException("Port must be between 1 and 65535, got: {$port}");
        }

        return $port;
    }
}
