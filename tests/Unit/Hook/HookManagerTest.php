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
        $output = '';
        hook()->on('SAVE', function () use (&$output) {
            $output .= 'Saved!';
        });

        hook()->fire('SAVE');

        $this->assertSame('Saved!', $output);
    }

    public function testFireWithArguments()
    {
        config()->set('hooks', ['NOTIFY']);

        $output = '';
        hook()->on('NOTIFY', function ($args) use (&$output) {
            $output .= 'Notified ' . $args['user'];
        });

        hook()->fire('NOTIFY', ['user' => 'John']);

        $this->assertSame('Notified John', $output);
    }

    public function testMultipleListeners()
    {
        $output = '';
        hook()->on('SAVE', function () use (&$output) {
            $output .= 'A';
        });

        hook()->on('SAVE', function () use (&$output) {
            $output .= 'B';
        });

        hook()->fire('SAVE');

        $this->assertSame('AB', $output);
    }

    public function testHookIsFiredOnlyOncePerListener()
    {
        $output = '';
        hook()->on('SAVE', function () use (&$output) {
            $output .= 'Once';
        });

        hook()->fire('SAVE');
        hook()->fire('SAVE');

        $this->assertSame('Once', $output);
    }

    public function testUnregisteredHookThrowsOnOn()
    {
        $this->expectException(HookException::class);
        $this->expectExceptionMessage('The Hook `INVALID` was not registered.');

        hook()->on('INVALID', function () {
        });
    }

    public function testUnregisteredHookThrowsOnFire()
    {
        $this->expectException(HookException::class);
        $this->expectExceptionMessage('The Hook `INVALID` was not registered.');

        hook()->fire('INVALID');
    }

    public function testGetRegisteredReturnsHookStore()
    {
        hook()->on('SAVE', function () {
        });

        $store = HookManager::getRegistered();

        $this->assertArrayHasKey('SAVE', $store);
        $this->assertCount(1, $store['SAVE']);
    }
}
