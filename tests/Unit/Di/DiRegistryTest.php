<?php

namespace Quantum\Tests\Unit\Di;

use Quantum\Di\Exceptions\DiException;
use Quantum\Di\DiRegistry;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Loader\Setup;
use Quantum\Http\Request;

class DiRegistryTest extends AppTestCase
{
    private DiRegistry $registry;

    public function setUp(): void
    {
        parent::setUp();
        $this->registry = new DiRegistry();
    }

    public function testRegisterDependency(): void
    {
        $this->registry->register(Setup::class);

        $this->assertTrue($this->registry->isRegistered(Setup::class));
        $this->assertSame(Setup::class, $this->registry->getConcrete(Setup::class));
    }

    public function testRegisterDependencies(): void
    {
        $this->registry->registerDependencies([
            \ArrayAccess::class => \ArrayObject::class,
            Setup::class => Setup::class,
        ]);

        $this->assertSame(\ArrayObject::class, $this->registry->getConcrete(\ArrayAccess::class));
        $this->assertSame(Setup::class, $this->registry->getConcrete(Setup::class));
    }

    public function testAttemptingToRegisterAlreadyRegisteredDependency(): void
    {
        $this->expectException(DiException::class);
        $this->expectExceptionMessage('The dependency `Quantum\Loader\Setup` is already registered.');

        $this->registry->register(Setup::class);
        $this->registry->register(Setup::class);
    }

    public function testAttemptingToRegisterNonExistentClass(): void
    {
        $this->expectException(DiException::class);
        $this->expectExceptionMessage('The dependency `NonExistentClass` is not instantiable.');

        $this->registry->register('NonExistentClass');
    }

    public function testAttemptingToRegisterAbstractClass(): void
    {
        $this->expectException(DiException::class);
        $this->expectExceptionMessage('The dependency `Quantum\\App\\Exceptions\\BaseException` is not instantiable.');

        $this->registry->register(\Quantum\App\Exceptions\BaseException::class);
    }

    public function testAttemptingToRegisterNonExistentAbstract(): void
    {
        $this->expectException(DiException::class);
        $this->expectExceptionMessage('The dependency `NonExistentInterface` is not valid abstract class.');

        $this->registry->register(Request::class, 'NonExistentInterface');
    }

    public function testGetConcreteThrowsForUnregisteredDependency(): void
    {
        $this->expectException(DiException::class);
        $this->expectExceptionMessage('The dependency `Quantum\Loader\Setup` is not registered.');

        $this->registry->getConcrete(Setup::class);
    }

    public function testResetClearsDependencies(): void
    {
        $this->registry->register(Setup::class);

        $this->assertTrue($this->registry->isRegistered(Setup::class));

        $this->registry->reset();

        $this->assertFalse($this->registry->isRegistered(Setup::class));
    }
}
