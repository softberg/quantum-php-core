<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Symfony\Component\Console\Tester\CommandTester;
use Quantum\Console\Commands\ServeCommand;
use Quantum\Tests\Unit\AppTestCase;

class ServeCommandTest extends AppTestCase
{
    private ServeCommand $command;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new ServeCommand();
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('serve', $this->command->getName());
        $this->assertSame('Serves the application on the PHP development server', $this->command->getDescription());
    }

    public function testCommandOptionsAreRegistered(): void
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasOption('host'));
        $this->assertTrue($definition->hasOption('port'));
        $this->assertTrue($definition->hasOption('open'));
    }

    public function testBrowserCommandReturnsArrayForKnownPlatform(): void
    {
        $method = new \ReflectionMethod($this->command, 'browserCommand');
        $method->setAccessible(true);

        $result = $method->invoke($this->command, 'http://localhost:8000');

        if (in_array(PHP_OS_FAMILY, ['Windows', 'Linux', 'Darwin'], true)) {
            $this->assertIsArray($result);
            $this->assertCount(2, $result);
            $this->assertSame('http://localhost:8000', $result[1]);
        } else {
            $this->assertNull($result);
        }
    }

    public function testExecUsesResolvedHostAndPortFlow(): void
    {
        $command = new class extends ServeCommand {
            public string $receivedHost = '';
            public int $receivedPort = 0;

            /** @var array<string, mixed> */
            public array $receivedServerData = [];

            protected function startServerOnAvailablePort(string $host, int $startPort): array
            {
                $this->receivedHost = $host;
                $this->receivedPort = $startPort;

                return [
                    'process' => fopen('php://memory', 'r'),
                    'port' => $startPort,
                    'url' => "http://{$host}:{$startPort}",
                ];
            }

            protected function handleServerExecution(array $serverData): void
            {
                $this->receivedServerData = $serverData;
            }
        };

        $tester = new CommandTester($command);
        $tester->execute([
            '--host' => '127.0.0.1',
            '--port' => '8011',
        ]);

        $this->assertSame('127.0.0.1', $command->receivedHost);
        $this->assertSame(8011, $command->receivedPort);
        $this->assertSame('http://127.0.0.1:8011', $command->receivedServerData['url']);
    }
}
