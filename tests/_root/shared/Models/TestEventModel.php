<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\QtModel;

class TestEventModel extends QtModel
{
    public $table = 'events';

    public function relations(): array
    {
        return [
            TestUserEventModel::class => [
                'foreign_key' => 'event_id',
                'local_key' => 'id'
            ]
        ];
    }
}
