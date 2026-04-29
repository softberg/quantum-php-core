<?php

namespace Quantum\Tests\Unit\App\Exceptions;

use Quantum\App\Exceptions\BaseException;
use Quantum\Tests\Unit\AppTestCase;

class BaseExceptionStub extends BaseException
{
}

class BaseExceptionTest extends AppTestCase
{
    public function testMethodNotSupportedFactory(): void
    {
        $exception = BaseExceptionStub::methodNotSupported('foo', 'Bar');

        $this->assertInstanceOf(BaseExceptionStub::class, $exception);
        $this->assertSame('The method `foo` is not supported for `Bar`.', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }

    public function testAdapterAndDriverFactories(): void
    {
        $adapterException = BaseExceptionStub::adapterNotSupported('x');
        $driverException = BaseExceptionStub::driverNotSupported('y');

        $this->assertSame('The adapter `x` is not supported.', $adapterException->getMessage());
        $this->assertSame(E_ERROR, $adapterException->getCode());
        $this->assertSame('The driver `y` is not supported.', $driverException->getMessage());
        $this->assertSame(E_ERROR, $driverException->getCode());
    }

    public function testLookupAndConnectionFactories(): void
    {
        $fileException = BaseExceptionStub::fileNotFound('a.php');
        $notFoundException = BaseExceptionStub::notFound('Route', 'home');
        $instanceException = BaseExceptionStub::notInstanceOf('Foo', 'Bar');
        $connectException = BaseExceptionStub::cantConnect('redis');

        $this->assertSame('The file `a.php` not found.', $fileException->getMessage());
        $this->assertSame(E_ERROR, $fileException->getCode());
        $this->assertSame('Route `home` not found.', $notFoundException->getMessage());
        $this->assertSame(E_ERROR, $notFoundException->getCode());
        $this->assertSame('The `Foo` is not instance of `Bar`.', $instanceException->getMessage());
        $this->assertSame(E_ERROR, $instanceException->getCode());
        $this->assertSame('Can not connect to `redis`.', $connectException->getMessage());
        $this->assertSame(E_ERROR, $connectException->getCode());
    }

    public function testConfigAndRequestMethodFactories(): void
    {
        $configException = BaseExceptionStub::missingConfig('database');
        $methodException = BaseExceptionStub::requestMethodNotAvailable('PATCH');

        $this->assertSame('Could not load config `database` properly.', $configException->getMessage());
        $this->assertSame(E_ERROR, $configException->getCode());
        $this->assertSame('Provided request method `PATCH` is not available.', $methodException->getMessage());
        $this->assertSame(E_WARNING, $methodException->getCode());
    }
}

