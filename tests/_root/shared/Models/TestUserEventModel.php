<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\QtModel;

class TestUserEventModel extends QtModel
{

    public $table = 'user_events';

    public function relations(): array
    {
        return [
            TestUserModel::class => [
                'foreign_key' => 'user_id',
                'local_key' => 'id'
            ],
            TestEventModel::class => [
                'foreign_key' => 'event_id',
                'local_key' => 'id'
            ]
        ];
    }
}
