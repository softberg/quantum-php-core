<?php

namespace Quantum\Tests\Unit\App\Adapters;

use Quantum\App\Adapters\WebAppAdapter;
use PHPUnit\Framework\TestCase;
use Quantum\App\Enums\AppType;
use Quantum\Di\DiContainer;
use Quantum\App\AppContext;
use Quantum\App\App;
use Quantum\Di\Di;

class WebAppAdapterTest extends TestCase
{
    private WebAppAdapter $webAppAdapter;

    private function createContext(string $mode = AppType::WEB): AppContext
    {
        $container = new DiContainer();
        Di::setCurrent($container);

        return new AppContext($mode, PROJECT_ROOT, $container);
    }

    public function setUp(): void
    {
        App::setBaseDir(PROJECT_ROOT);

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
