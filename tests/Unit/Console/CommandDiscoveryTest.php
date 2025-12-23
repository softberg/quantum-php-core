<?php

namespace Quantum\Tests\Unit\Console;

use Quantum\Console\CommandDiscovery;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Console\QtCommand;

class CommandDiscoveryTest extends AppTestCase
{

    public function testDiscoverCoreCommands()
    {
        $commandsDirectory = base_dir()
            . DIRECTORY_SEPARATOR . 'shared'
            . DIRECTORY_SEPARATOR . 'Commands';

        $commandsNamespace = '\\Quantum\\Tests\\_root\\shared\\Commands\\';

        $commands = CommandDiscovery::discover($commandsDirectory, $commandsNamespace);

        $this->assertIsArray($commands, 'Discover should return an array');

        $this->assertNotEmpty($commands, 'No core commands were discovered');

        $command = $commands[0];

        $this->assertArrayHasKey('class', $command);

        $this->assertArrayHasKey('name', $command);

        $this->assertArrayHasKey('description', $command);

        $this->assertArrayHasKey('help', $command);

        $this->assertTrue(class_exists($command['class']));

        $instance = new $command['class']();

        $this->assertInstanceOf(QtCommand::class, $instance);

        $this->assertNotEmpty($instance->getName());
    }
}