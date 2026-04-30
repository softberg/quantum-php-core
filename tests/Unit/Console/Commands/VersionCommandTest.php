<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Symfony\Component\Console\Tester\CommandTester;
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

    public function testExecPrintsFrameworkVersion(): void
    {
        config()->set('app', ['version' => '3.0.0']);

        $tester = new CommandTester($this->command);
        $fontPath = assets_dir() . DS . 'shared' . DS . 'fonts' . DS . 'figlet' . DS . 'slant.flf';

        if (!is_file($fontPath)) {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('slant.flf');
        }

        $tester->execute([]);
        $output = $tester->getDisplay();
        $this->assertStringContainsString('3.0.0', $output);
    }
}
