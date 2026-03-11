<?php

namespace Quantum\Tests\_root\shared\Commands;

use Quantum\Console\QtCommand;

class TestCommand extends QtCommand
{
    protected ?string $name = 'test:dummy';
    protected ?string $description = 'Dummy test command';
    protected ?string $help = 'Used only for core command discovery tests';

    protected array $args = [
        ['uuid', 'optional', 'User uuid'],
    ];

    protected array $options = [
        ['force', 'f', 'none', 'Force execution'],
    ];

    public function exec(): void
    {
        // intentionally empty
    }
}
