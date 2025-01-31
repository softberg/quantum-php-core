<?php

namespace Quantum\Tests\App\Adapters;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
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
        App::setBaseDir(dirname(__DIR__, 2) . DS . '_root');

        $fs = FileSystemFactory::get();

        if (!$fs->exists(App::getBaseDir() . DS . '.env.testing')) {
            $fs->copy(
                App::getBaseDir() . DS . '.env.example',
                App::getBaseDir() . DS . '.env.testing'
            );
        }

        $applicationMock = Mockery::mock(Application::class)->makePartial();
        $applicationMock->shouldReceive('getName')->andReturn('Qt Console Application');
        $applicationMock->shouldReceive('run')->andReturn(0);

        $this->consoleAppAdapter = Mockery::mock(ConsoleAppAdapter::class)->makePartial();
        $this->consoleAppAdapter->shouldReceive('createApplication')
            ->withArgs(['Qt Console Application', '2.x'])
            ->andReturn($applicationMock);

        $this->consoleAppAdapter->__construct();
    }

    public function testConsoleAppAdapterStartSuccessfully()
    {
        $_SERVER['argv'] = ['qt', 'list', '--quiet'];

        $result = $this->consoleAppAdapter->start();

        $this->assertEquals(0, $result);
    }

    public function testWebAppAdapterStartFails()
    {
        $_SERVER['argv'] = ['qt', 'unknown', '--quiet'];

        $this->expectException(Exception::class);

        $this->consoleAppAdapter->start();
    }
}
