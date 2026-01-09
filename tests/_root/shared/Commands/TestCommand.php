<?php

namespace Quantum\Tests\_root\shared\Commands;

use Quantum\Console\QtCommand;

class TestCommand extends QtCommand
{
    protected $name = 'test:dummy';
    protected $description = 'Dummy test command';
    protected $help = 'Used only for core command discovery tests';

    protected $args = [
        ['uuid', 'optional', 'User uuid'],
    ];

    protected $options = [
        ['force', 'f', 'none', 'Force execution'],
    ];

    public function exec()
    {
        // intentionally empty
    }
}
