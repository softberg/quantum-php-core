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

    public function testGetInstanceReturnsSameObject(): void
    {
        $instance1 = HookManager::getInstance();
        $instance2 = HookManager::getInstance();

        $this->assertInstanceOf(HookManager::class, $instance1);
        $this->assertSame($instance1, $instance2);
    }

    public function testOnAndFireOutputsCorrectly(): void
    {
        $output = '';
        hook()->on('SAVE', function () use (&$output): void {
            $output .= 'Saved!';
        });

        hook()->fire('SAVE');

        $this->assertSame('Saved!', $output);
    }

    public function testFireWithArguments(): void
    {
        config()->set('hooks', ['NOTIFY']);

        $output = '';
        hook()->on('NOTIFY', function (array $args) use (&$output): void {
            $output .= 'Notified ' . $args['user'];
        });

        hook()->fire('NOTIFY', ['user' => 'John']);

        $this->assertSame('Notified John', $output);
    }

    public function testMultipleListeners(): void
    {
        $output = '';
        hook()->on('SAVE', function () use (&$output): void {
            $output .= 'A';
        });

        hook()->on('SAVE', function () use (&$output): void {
            $output .= 'B';
        });

        hook()->fire('SAVE');

        $this->assertSame('AB', $output);
    }

    public function testHookIsFiredOnlyOncePerListener(): void
    {
        $output = '';
        hook()->on('SAVE', function () use (&$output): void {
            $output .= 'Once';
        });

        hook()->fire('SAVE');
        hook()->fire('SAVE');

        $this->assertSame('Once', $output);
    }

    public function testUnregisteredHookThrowsOnOn(): void
    {
        $this->expectException(HookException::class);
        $this->expectExceptionMessage('The Hook `INVALID` was not registered.');

        hook()->on('INVALID', function (): void {
        });
    }

    public function testUnregisteredHookThrowsOnFire(): void
    {
        $this->expectException(HookException::class);
        $this->expectExceptionMessage('The Hook `INVALID` was not registered.');

        hook()->fire('INVALID');
    }

    public function testGetRegisteredReturnsHookStore(): void
    {
        hook()->on('SAVE', function (): void {
        });

        $store = HookManager::getRegistered();

        $this->assertArrayHasKey('SAVE', $store);
        $this->assertCount(1, $store['SAVE']);
    }
}
