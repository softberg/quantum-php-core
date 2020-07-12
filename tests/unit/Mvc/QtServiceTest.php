<?php

namespace Quantum\Test\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Quantum\Mvc\QtService;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class QtServiceTest extends TestCase
{

    public function setUp(): void
    {
        $this->helperMock = Mockery::mock('overload:Quantum\Helpers\Helper');

        $this->helperMock->shouldReceive('_message')->andReturnUsing(function($subject, $params) {
            if (is_array($params)) {
                return preg_replace_callback('/{%\d+}/', function () use (&$params) {
                    return array_shift($params);
                }, $subject);
            } else {
                return preg_replace('/{%\d+}/', $params, $subject);
            }
        });
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
