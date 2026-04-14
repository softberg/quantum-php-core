<?php

namespace Quantum\Tests\Unit\App;

use Quantum\App\Adapters\ConsoleAppAdapter;
use Quantum\App\Exceptions\AppException;
use Quantum\App\Adapters\WebAppAdapter;
use Quantum\App\Contracts\AppInterface;
use PHPUnit\Framework\TestCase;
use Quantum\App\Enums\AppType;
use Quantum\Di\DiContainer;
use Quantum\App\AppContext;
use Quantum\App\App;
use Quantum\Di\Di;

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
class AppTest extends TestCase
{
    private function createContext(string $mode = AppType::WEB): AppContext
    {
        $container = new DiContainer();
        Di::setCurrent($container);

        return new AppContext($mode, PROJECT_ROOT, $container);
    }

    public function setUp(): void
    {
        parent::setUp();

        Di::reset();

        App::setBaseDir(PROJECT_ROOT);
    }

    public function tearDown(): void
    {
        config()->flush();
        Di::reset();
    }

    public function testAppGetAdapter(): void
    {
        $app = new App(new WebAppAdapter($this->createContext()));

        $this->assertInstanceOf(WebAppAdapter::class, $app->getAdapter());

        $this->assertInstanceOf(AppInterface::class, $app->getAdapter());

        config()->flush();
        Di::reset();

        App::setBaseDir(PROJECT_ROOT);

        $app = new App(new ConsoleAppAdapter($this->createContext(AppType::CONSOLE)));

        $this->assertInstanceOf(ConsoleAppAdapter::class, $app->getAdapter());

        $this->assertInstanceOf(AppInterface::class, $app->getAdapter());
    }

    public function testAppCallingValidMethod(): void
    {
        $app = new App(new WebAppAdapter($this->createContext()));

        request()->create('GET', '/test/am/tests');

        ob_start();
        $this->assertEquals(0, $app->start());
        ob_end_clean();
    }

    public function testAppCallingInvalidMethod(): void
    {
        $app = new App(new WebAppAdapter($this->createContext()));

        $this->expectException(AppException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `' . WebAppAdapter::class . '`');

        $app->callingInvalidMethod();
    }
}
