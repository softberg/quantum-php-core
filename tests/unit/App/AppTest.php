<?php

namespace Quantum\Tests\App;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\App\Adapters\ConsoleAppAdapter;
use Quantum\App\Exceptions\AppException;
use Quantum\App\Adapters\WebAppAdapter;
use Quantum\App\Contracts\AppInterface;
use PHPUnit\Framework\TestCase;
use Quantum\Http\Request;
use Quantum\App\App;
use Quantum\Di\Di;

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
class AppTest extends TestCase
{

    private $fs;

    public function setUp(): void
    {
        parent::setUp();

        App::setBaseDir(dirname(__DIR__) . DS . '_root');

        $this->fs = FileSystemFactory::get();

        if (! $this->fs->exists(App::getBaseDir() . DS . '.env.testing')) {
            $this->fs->copy(
                App::getBaseDir() . DS . '.env.example',
                App::getBaseDir() . DS . '.env.testing'
            );
        }
    }

    public function tearDown(): void
    {
        if ($this->fs->exists(App::getBaseDir() . DS . '.env.testing')) {
            $this->fs->remove(App::getBaseDir() . DS . '.env.testing');
        }
    }

    public function testAppGetAdapter()
    {
        $app = new App(new WebAppAdapter());

        $this->assertInstanceOf(WebAppAdapter::class, $app->getAdapter());

        $this->assertInstanceOf(AppInterface::class, $app->getAdapter());

        $app = new App(new ConsoleAppAdapter());

        $this->assertInstanceOf(ConsoleAppAdapter::class, $app->getAdapter());

        $this->assertInstanceOf(AppInterface::class, $app->getAdapter());
    }

    public function testAppCallingValidMethod()
    {
        $app = new App(new WebAppAdapter());

        $request = Di::get(Request::class);
        $request->create('GET', '/test/am/tests');

        $this->assertEquals(0, $app->start());
    }

    public function testAppCallingInvalidMethod()
    {
        $app = new App(new WebAppAdapter());

        $this->expectException(AppException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `'. WebAppAdapter::class .'`');

        $app->callingInvalidMethod();
    }
}