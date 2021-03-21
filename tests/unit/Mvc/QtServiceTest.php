<?php

namespace Quantum\Test\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Quantum\Mvc\QtService;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Loader\Loader;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class QtServiceTest extends TestCase
{

    public function setUp(): void
    {
        $loader = new Loader(new FileSystem);

        $loader->loadDir(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers' . DS . 'functions');
    }

    public function testGetInstance()
    {
        $this->assertInstanceOf('Quantum\Mvc\QtService', QtService::getInstance());
    }

    /**
     * @runInSeparateProcess
     */
    public function testMissingeMethods()
    {
        $this->expectException(\BadMethodCallException::class);

        $this->expectExceptionMessage('The method `undefinedMethod` is not defined');

        $service = QtService::getInstance();

        $service->undefinedMethod();
    }

}
