<?php

namespace Quantum\Tests\App\Adapters;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\App\Adapters\WebAppAdapter;
use PHPUnit\Framework\TestCase;
use Quantum\Http\Request;
use Quantum\App\App;
use Quantum\Di\Di;
use Exception;

class WebAppAdapterTest extends TestCase
{

    private $webAppAdapter;

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

        $this->webAppAdapter = new WebAppAdapter();
    }

    public function testWebAppAdapterStartSuccessfully()
    {
        $request = Di::get(Request::class);
        $request->create('GET', '/test/am/tests');

        $result = $this->webAppAdapter->start();

        $this->assertEquals(0, $result);
    }

    public function testWebAppAdapterStartFails()
    {
        $request = Di::get(Request::class);
        $request->create('POST', '');

        $this->expectException(Exception::class);

        $this->webAppAdapter->start();
    }
}
