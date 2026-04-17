<?php

namespace Quantum\Tests\Unit\App\Adapters;

use Quantum\App\Adapters\ConsoleAppAdapter;
use Symfony\Component\Console\Application;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\App\Enums\AppType;
use Exception;
use Mockery;

class ConsoleAppAdapterTest extends AppTestCase
{
    private $consoleAppAdapter;

    public function setUp(): void
    {
        $applicationMock = Mockery::mock(Application::class)->makePartial();
        $applicationMock->shouldReceive('getName')->andReturn('Qt Console Application');
        $applicationMock->shouldReceive('run')->andReturn(0);

        $this->consoleAppAdapter = Mockery::mock(ConsoleAppAdapter::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $this->consoleAppAdapter
            ->shouldReceive('createApplication')
            ->andReturn($applicationMock);
    }

    public function tearDown(): void
    {
        config()->flush();
        $this->clearAppContext();
    }

    public function testConsoleAppAdapterStartSuccessfully(): void
    {
        $_SERVER['argv'] = ['qt', 'list', '--quiet'];

        $this->consoleAppAdapter->__construct($this->createContext(AppType::CONSOLE));

        $result = $this->consoleAppAdapter->start();

        $this->assertEquals(0, $result);
    }

    public function testConsoleAppAdapterStartFails(): void
    {
        $_SERVER['argv'] = ['qt', 'unknown', '--quiet'];

        $this->consoleAppAdapter->__construct($this->createContext(AppType::CONSOLE));

        $this->expectException(Exception::class);

        $this->consoleAppAdapter->start();
    }
}
