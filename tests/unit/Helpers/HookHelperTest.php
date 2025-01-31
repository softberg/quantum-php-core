<?php

namespace Quantum\Tests\Helpers;

use Quantum\Hooks\Exceptions\HookException;
use Quantum\Hooks\HookManager;
use Quantum\Tests\AppTestCase;

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
        hook()->on('SAVE', function () {
            echo 'Data successfully saved';
        });

        hook()->fire('SAVE');

        $this->expectOutputString('Data successfully saved');
    }

    public function testHookFireWithArgument()
    {
        hook()->on('SAVE', function ($data) {
            echo 'The file ' . $data['filename'] . ' was successfully saved';
        });

        hook()->fire('SAVE', ['filename' => 'doc.pdf']);

        $this->expectOutputString('The file doc.pdf was successfully saved');
    }

    public function testHookMultipleListeners()
    {
        hook()->on('SAVE', function ($data) {
            echo 'The file ' . $data['filename'] . ' was successfully saved' . PHP_EOL;
        });

        hook()->on('SAVE', function () {
            echo 'The email was successfully sent';
        });

        hook()->fire('SAVE', ['filename' => 'doc.pdf']);

        $this->expectOutputString('The file doc.pdf was successfully saved' . PHP_EOL . 'The email was successfully sent');
    }

    public function testUnregisteredHookAtOn()
    {
        $this->expectException(HookException::class);

        $this->expectExceptionMessage('unregistered_hook_name');

        hook()->on('SOME_EVENT', function () {
            echo 'Do someting';
        });
    }

    public function testUnregisteredHookAtFire()
    {
        $this->expectException(HookException::class);

        $this->expectExceptionMessage('unregistered_hook_name');

        hook()->fire('SOME_EVENT');
    }
}