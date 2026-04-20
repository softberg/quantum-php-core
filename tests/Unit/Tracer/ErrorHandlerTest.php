<?php

namespace Quantum\Tests\Unit\Tracer;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Tracer\ErrorHandler;
use Quantum\Logger\Logger;
use ErrorException;
use Mockery;

class ErrorHandlerTest extends AppTestCase
{
    private ErrorHandler $errorHandler;

    public function setUp(): void
    {
        parent::setUp();

        $this->errorHandler = new ErrorHandler();
    }

    public function tearDown(): void
    {
        restore_error_handler();
        restore_exception_handler();
        parent::tearDown();
    }

    public function testSetupRegistersErrorHandler(): void
    {
        $logger = Mockery::mock(Logger::class);

        $this->errorHandler->setup($logger);

        $currentHandler = set_error_handler(function () {
        });
        restore_error_handler();

        $this->assertNotNull($currentHandler);
        $this->assertIsArray($currentHandler);
        $this->assertInstanceOf(ErrorHandler::class, $currentHandler[0]);
        $this->assertEquals('handleError', $currentHandler[1]);
    }

    public function testSetupRegistersExceptionHandler(): void
    {
        $logger = Mockery::mock(Logger::class);

        $this->errorHandler->setup($logger);

        $currentHandler = set_exception_handler(function () {
        });
        restore_exception_handler();

        $this->assertNotNull($currentHandler);
        $this->assertIsArray($currentHandler);
        $this->assertInstanceOf(ErrorHandler::class, $currentHandler[0]);
        $this->assertEquals('handleException', $currentHandler[1]);
    }

    public function testHandleErrorThrowsErrorException(): void
    {
        $oldLevel = error_reporting(E_ALL);

        try {
            $this->errorHandler->handleError(E_WARNING, 'Test error', __FILE__, __LINE__);
            $this->fail('Expected ErrorException was not thrown');
        } catch (ErrorException $e) {
            $this->assertEquals('Test error', $e->getMessage());
            $this->assertEquals(E_WARNING, $e->getSeverity());
        } finally {
            error_reporting($oldLevel);
        }
    }

    public function testHandleErrorReturnsFalseForSuppressedErrors(): void
    {
        $oldLevel = error_reporting(0);

        try {
            $result = $this->errorHandler->handleError(E_NOTICE, 'Suppressed', __FILE__, __LINE__);
            $this->assertFalse($result);
        } finally {
            error_reporting($oldLevel);
        }
    }

    public function testErrorTypesConstant(): void
    {
        $this->assertEquals('error', ErrorHandler::ERROR_TYPES[E_ERROR]);
        $this->assertEquals('warning', ErrorHandler::ERROR_TYPES[E_WARNING]);
        $this->assertEquals('notice', ErrorHandler::ERROR_TYPES[E_NOTICE]);
        $this->assertEquals('error', ErrorHandler::ERROR_TYPES[E_PARSE]);
    }
}
