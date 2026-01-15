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
use Throwable;

/**
 * Class ServeCommand
 * @package Quantum\Console
 */
class ServeCommand extends QtCommand
{
    /**
     * Platform Windows
     */
    public const PLATFORM_WINDOWS = 'Windows';

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
     * Execute the command.
     */
    public function exec()
    {
        $host = $this->host();
        $startPort = $this->port();

        $serverProcess = $this->startServerOnAvailablePort($host, $startPort);

        $this->handleServerExecution($serverProcess);
    }

    /**
     * Start server on first available port.
     * @param string $host
     * @param int $startPort
     * @return array
     * @throws RuntimeException
     */
    protected function startServerOnAvailablePort(string $host, int $startPort): array
    {
        for ($i = 0; $i < $this->maxPortScan && $startPort + $i <= 65535; $i++) {
            $port = $startPort + $i;

            if ($this->isPortInUse($host, $port)) {
                continue;
            }

            $url = "http://{$host}:{$port}";
            $this->info("Starting development server at: {$url}");

            $process = $this->startPhpServer($host, $port);

            try {
                $this->waitUntilServerIsReady($host, $port, $process);

                return [
                    'process' => $process,
                    'port' => $port,
                    'url' => $url,
                ];
            } catch (Throwable $e) {
                $this->cleanupProcess($process);
            }
        }

        throw new RuntimeException('Unable to start PHP server on any available port.');
    }

    /**
     * Handle server execution (browser opening and process monitoring).
     * @param array $serverData
     */
    protected function handleServerExecution(array $serverData): void
    {
        if ($this->shouldOpenBrowser()) {
            $this->openBrowser($serverData['url']);
        }

        $this->waitForProcess($serverData['process']);
    }

    /**
     * Clean up process resource.
     * @param resource $process
     */
    protected function cleanupProcess($process): void
    {
        if (is_resource($process)) {
            proc_close($process);
        }
    }

    /**
     * Check if port is already in use by another process.
     * @param string $host
     * @param int $port
     * @return bool
     */
    protected function isPortInUse(string $host, int $port): bool
    {
        $fp = @fsockopen($host, $port, $errno, $errstr, 0.1);
        if ($fp) {
            fclose($fp);
            return true;
        }
        return false;
    }

    /**
     * Start PHP built-in server.
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
     * @param resource $process
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
     * Block until the PHP server process exits.
     * @param resource $process
     */
    protected function waitForProcess($process): void
    {
        while (proc_get_status($process)['running']) {
            usleep(200_000);
        }

        proc_close($process);
    }

    /**
     * Determine whether the browser should be opened.
     * @return bool
     */
    protected function shouldOpenBrowser(): bool
    {
        return (bool) $this->getOption('open');
    }

    /**
     * Open the default browser.
     * @param string $url
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
     * Resolve platform-specific browser command.
     * @param string $url
     * @return array|null
     */
    protected function browserCommand(string $url): ?array
    {
        switch (PHP_OS_FAMILY) {
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
     * Get host option.
     * @return string
     */
    protected function host(): string
    {
        return (string) ($this->getOption('host') ?: $this->defaultHost);
    }

    /**
     * Get and validate port option.
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
