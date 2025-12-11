<?php

namespace Quantum\Tests\Unit\Hook;

use Quantum\Hook\Exceptions\HookException;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Hook\HookManager;

class HookManagerTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(HookManager::class, 'instance', null);
        $this->setPrivateProperty(HookManager::class, 'store', []);
    }

    public function testGetInstanceReturnsSameObject()
    {
        $instance1 = HookManager::getInstance();
        $instance2 = HookManager::getInstance();

        $this->assertInstanceOf(HookManager::class, $instance1);
        $this->assertSame($instance1, $instance2);
    }

    public function testOnAndFireOutputsCorrectly()
    {
        hook()->on('SAVE', function () {
            echo 'Saved!';
        });

        hook()->fire('SAVE');

        $this->expectOutputString('Saved!');
    }

    public function testFireWithArguments()
    {
        config()->set('hooks', ['NOTIFY']);

        hook()->on('NOTIFY', function ($args) {
            echo 'Notified ' . $args['user'];
        });

        hook()->fire('NOTIFY', ['user' => 'John']);

        $this->expectOutputString('Notified John');
    }

    public function testMultipleListeners()
    {
        hook()->on('SAVE', function () {
            echo 'A';
        });

        hook()->on('SAVE', function () {
            echo 'B';
        });

        hook()->fire('SAVE');

        $this->expectOutputString('AB');
    }

    public function testHookIsFiredOnlyOncePerListener()
    {
        hook()->on('SAVE', function () {
            echo 'Once';
        });

        hook()->fire('SAVE');
        hook()->fire('SAVE');

        $this->expectOutputString('Once');
    }

    public function testUnregisteredHookThrowsOnOn()
    {
        $this->expectException(HookException::class);
        $this->expectExceptionMessage('The Hook `INVALID` was not registered.');

        hook()->on('INVALID', function () {});
    }

    public function testUnregisteredHookThrowsOnFire()
    {
        $this->expectException(HookException::class);
        $this->expectExceptionMessage('The Hook `INVALID` was not registered.');

        hook()->fire('INVALID');
    }

    public function testGetRegisteredReturnsHookStore()
    {
        hook()->on('SAVE', function () {});

        $store = HookManager::getRegistered();

        $this->assertArrayHasKey('SAVE', $store);
        $this->assertCount(1, $store['SAVE']);
    }
}
