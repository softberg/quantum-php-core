<?php

namespace Quantum\Tests\Unit\Hook\Helpers;

use Quantum\Hook\Exceptions\HookException;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Hook\HookManager;

class HookHelperTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testHookHelperInstance(): void
    {
        $this->assertInstanceOf(HookManager::class, hook());
    }

    public function testHookOnAndFire(): void
    {
        $output = '';
        hook()->on('SAVE', function () use (&$output): void {
            $output .= 'Data successfully saved';
        });

        hook()->fire('SAVE');

        $this->assertSame('Data successfully saved', $output);
    }

    public function testHookFireWithArgument(): void
    {
        $output = '';
        hook()->on('SAVE', function (array $data) use (&$output): void {
            $output .= 'The file ' . $data['filename'] . ' was successfully saved';
        });

        hook()->fire('SAVE', ['filename' => 'doc.pdf']);

        $this->assertSame('The file doc.pdf was successfully saved', $output);
    }

    public function testHookMultipleListeners(): void
    {
        $output = '';
        hook()->on('SAVE', function (array $data) use (&$output): void {
            $output .= 'The file ' . $data['filename'] . ' was successfully saved' . PHP_EOL;
        });

        hook()->on('SAVE', function () use (&$output): void {
            $output .= 'The email was successfully sent';
        });

        hook()->fire('SAVE', ['filename' => 'doc.pdf']);

        $this->assertSame('The file doc.pdf was successfully saved' . PHP_EOL . 'The email was successfully sent', $output);
    }

    public function testUnregisteredHookAtOn(): void
    {
        $this->expectException(HookException::class);

        $this->expectExceptionMessage('The Hook `SOME_EVENT` was not registered.');

        hook()->on('SOME_EVENT', function (): void {
            // No output needed
        });
    }

    public function testUnregisteredHookAtFire(): void
    {
        $this->expectException(HookException::class);

        $this->expectExceptionMessage('The Hook `SOME_EVENT` was not registered.');

        hook()->fire('SOME_EVENT');
    }
}
