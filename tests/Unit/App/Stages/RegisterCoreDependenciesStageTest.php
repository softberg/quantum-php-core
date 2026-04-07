<?php

namespace Quantum\Tests\Unit\App\Stages;

use Quantum\App\Stages\RegisterCoreDependenciesStage;
use Quantum\App\Enums\AppType;
use Quantum\App\AppContext;
use PHPUnit\Framework\TestCase;
use Quantum\Loader\Loader;
use Quantum\Http\Request;
use Quantum\Http\Response;
use Quantum\Di\Di;

class RegisterCoreDependenciesStageTest extends TestCase
{
    public function setUp(): void
    {
        Di::reset();
    }

    public function tearDown(): void
    {
        Di::reset();
    }

    public function testRegistersCoreDependencies(): void
    {
        $this->assertFalse(Di::isRegistered(Loader::class));
        $this->assertFalse(Di::isRegistered(Request::class));
        $this->assertFalse(Di::isRegistered(Response::class));

        $stage = new RegisterCoreDependenciesStage();
        $stage->process(new AppContext(AppType::WEB));

        $this->assertTrue(Di::isRegistered(Loader::class));
        $this->assertTrue(Di::isRegistered(Request::class));
        $this->assertTrue(Di::isRegistered(Response::class));
    }
}
