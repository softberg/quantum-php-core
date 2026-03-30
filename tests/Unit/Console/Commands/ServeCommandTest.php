<?php

namespace Quantum\Tests\Unit\Console\Commands;

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
}
