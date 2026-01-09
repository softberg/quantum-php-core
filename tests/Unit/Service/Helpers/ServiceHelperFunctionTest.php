<?php

namespace Quantum\Tests\Unit\Service\Helpers;

use Quantum\Tests\_root\shared\Services\TokenService;
use Quantum\Service\Exceptions\ServiceException;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Service\QtService;

class ServiceHelperFunctionTest extends AppTestCase
{
    public function testServiceReturnsQtServiceInstance()
    {
        $service = service(TokenService::class);

        $this->assertInstanceOf(TokenService::class, $service);

        $this->assertInstanceOf(QtService::class, $service);
    }

    public function testModelThrowsOnInvalidClass()
    {
        $this->expectException(ServiceException::class);

        service('NonExistentServiceClass');
    }
}
