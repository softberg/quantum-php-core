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

    public function testHookHelperInstance()
    {
        $this->assertInstanceOf(HookManager::class, hook());
    }

    public function testHookOnAndFire()
    {
        $output = '';
        hook()->on('SAVE', function () use (&$output) {
            $output .= 'Data successfully saved';
        });

        hook()->fire('SAVE');

        $this->assertSame('Data successfully saved', $output);
    }

    public function testHookFireWithArgument()
    {
        $output = '';
        hook()->on('SAVE', function ($data) use (&$output) {
            $output .= 'The file ' . $data['filename'] . ' was successfully saved';
        });

        hook()->fire('SAVE', ['filename' => 'doc.pdf']);

        $this->assertSame('The file doc.pdf was successfully saved', $output);
    }

    public function testHookMultipleListeners()
    {
        $output = '';
        hook()->on('SAVE', function ($data) use (&$output) {
            $output .= 'The file ' . $data['filename'] . ' was successfully saved' . PHP_EOL;
        });

        hook()->on('SAVE', function () use (&$output) {
            $output .= 'The email was successfully sent';
        });

        hook()->fire('SAVE', ['filename' => 'doc.pdf']);

        $this->assertSame('The file doc.pdf was successfully saved' . PHP_EOL . 'The email was successfully sent', $output);
    }

    public function testUnregisteredHookAtOn()
    {
        $this->expectException(HookException::class);

        $this->expectExceptionMessage('The Hook `SOME_EVENT` was not registered.');

        hook()->on('SOME_EVENT', function () {
            // No output needed
        });
    }

    public function testUnregisteredHookAtFire()
    {
        $this->expectException(HookException::class);

        $this->expectExceptionMessage('The Hook `SOME_EVENT` was not registered.');

        hook()->fire('SOME_EVENT');
    }
}
