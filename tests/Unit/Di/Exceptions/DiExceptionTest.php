<?php

namespace Quantum\Tests\Unit\Di\Exceptions;

use Quantum\Di\Exceptions\DiException;
use Quantum\Tests\Unit\AppTestCase;

class DiExceptionTest extends AppTestCase
{
    public function testDependencyFactories(): void
    {
        $notRegistered = DiException::dependencyNotRegistered('Foo');
        $alreadyRegistered = DiException::dependencyAlreadyRegistered('Bar');
        $notInstantiable = DiException::dependencyNotInstantiable('Baz');

        $this->assertSame('The dependency `Foo` is not registered.', $notRegistered->getMessage());
        $this->assertSame('The dependency `Bar` is already registered.', $alreadyRegistered->getMessage());
        $this->assertSame('The dependency `Baz` is not instantiable.', $notInstantiable->getMessage());
        $this->assertSame(E_ERROR, $notRegistered->getCode());
        $this->assertSame(E_ERROR, $alreadyRegistered->getCode());
        $this->assertSame(E_ERROR, $notInstantiable->getCode());
    }

    public function testAbstractAndCircularFactories(): void
    {
        $invalidAbstract = DiException::invalidAbstractDependency('Qux');
        $circular = DiException::circularDependency('A -> B -> A');

        $this->assertSame('The dependency `Qux` is not valid abstract class.', $invalidAbstract->getMessage());
        $this->assertSame('Circular dependency detected: `A -> B -> A`', $circular->getMessage());
        $this->assertSame(E_ERROR, $invalidAbstract->getCode());
        $this->assertSame(E_ERROR, $circular->getCode());
    }

    public function testInvalidCallableFactory(): void
    {
        $withEntry = DiException::invalidCallable('foo');
        $withoutEntry = DiException::invalidCallable();

        $this->assertSame(
            'Invalid callable provided: expected Closure or array-style callable `foo`',
            $withEntry->getMessage()
        );
        $this->assertSame(
            'Invalid callable provided: expected Closure or array-style callable ``',
            $withoutEntry->getMessage()
        );
        $this->assertSame(E_ERROR, $withEntry->getCode());
        $this->assertSame(E_ERROR, $withoutEntry->getCode());
    }
}
