<?php

namespace Quantum\Tests\Unit\App\Adapters;

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
        App::setBaseDir(PROJECT_ROOT);

        $this->webAppAdapter = new WebAppAdapter();
    }

    public function tearDown(): void
    {
        config()->flush();
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
