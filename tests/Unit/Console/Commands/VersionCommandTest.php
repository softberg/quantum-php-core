<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\VersionCommand;
use Quantum\Tests\Unit\AppTestCase;

class VersionCommandTest extends AppTestCase
{
    private VersionCommand $command;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new VersionCommand();
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('core:version', $this->command->getName());
        $this->assertSame('Core version', $this->command->getDescription());
        $this->assertSame('Printing the current version of the framework into the terminal', $this->command->getHelp());
    }
}
