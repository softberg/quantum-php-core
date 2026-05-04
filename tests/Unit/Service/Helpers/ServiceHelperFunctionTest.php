<?php

namespace Quantum\Tests\Unit\Service\Helpers;

use Quantum\Tests\_root\shared\Services\TokenService;
use Quantum\Service\Exceptions\ServiceException;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Service\Service;

class ServiceHelperFunctionTest extends AppTestCase
{
    public function testServiceReturnsServiceInstance(): void
    {
        $service = service(TokenService::class);

        $this->assertInstanceOf(TokenService::class, $service);

        $this->assertInstanceOf(Service::class, $service);
    }

    public function testModelThrowsOnInvalidClass(): void
    {
        $this->expectException(ServiceException::class);

        service('NonExistentServiceClass');
    }
}
