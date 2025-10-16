<?php

namespace Quantum\Tests\Unit\App\Adapters;

use Quantum\App\Adapters\ConsoleAppAdapter;
use Symfony\Component\Console\Application;
use PHPUnit\Framework\TestCase;
use Quantum\App\App;
use Exception;
use Mockery;

class ConsoleAppAdapterTest extends TestCase
{

    private $consoleAppAdapter;

    public function setUp(): void
    {
        App::setBaseDir(PROJECT_ROOT);

        $applicationMock = Mockery::mock(Application::class)->makePartial();
        $applicationMock->shouldReceive('getName')->andReturn('Qt Console Application');
        $applicationMock->shouldReceive('run')->andReturn(0);

        $this->consoleAppAdapter = Mockery::mock(ConsoleAppAdapter::class)->makePartial();
        $this->consoleAppAdapter->shouldReceive('createApplication')->andReturn($applicationMock);
    }

    public function tearDown(): void
    {
        config()->flush();
    }

    public function testConsoleAppAdapterStartSuccessfully()
    {
        $_SERVER['argv'] = ['qt', 'list', '--quiet'];

        $this->consoleAppAdapter->__construct();

        $result = $this->consoleAppAdapter->start();

        $this->assertEquals(0, $result);
    }

    public function testConsoleAppAdapterStartFails()
    {
        $_SERVER['argv'] = ['qt', 'unknown', '--quiet'];

        $this->consoleAppAdapter->__construct();

        $this->expectException(Exception::class);

        $this->consoleAppAdapter->start();
    }
}