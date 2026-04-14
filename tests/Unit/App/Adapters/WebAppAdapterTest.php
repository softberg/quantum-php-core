<?php

namespace Quantum\Tests\Unit\App\Adapters;

use Quantum\App\Adapters\WebAppAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Di\Di;

class WebAppAdapterTest extends AppTestCase
{
    private WebAppAdapter $webAppAdapter;

    public function setUp(): void
    {
        $this->webAppAdapter = new WebAppAdapter($this->createContext());
    }

    public function tearDown(): void
    {
        config()->flush();
        Di::reset();
    }

    public function testWebAppAdapterStartSuccessfully(): void
    {
        request()->create('GET', '/test/am/tests');

        ob_start();
        $result = $this->webAppAdapter->start();
        ob_end_clean();

        $this->assertEquals(0, $result);
    }

    public function testWebAppAdapterStartFails(): void
    {
        request()->create('POST', '');

        ob_start();
        $result = $this->webAppAdapter->start();
        ob_end_clean();

        $this->assertSame(0, $result);
    }

    public function testWebAppAdapterHandlesPageNotFoundGracefully(): void
    {
        request()->create('GET', '/non-existing-uri');

        ob_start();
        $result = $this->webAppAdapter->start();
        ob_end_clean();

        $this->assertSame(0, $result);
    }
}
