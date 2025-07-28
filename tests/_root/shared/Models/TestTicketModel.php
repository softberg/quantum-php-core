<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\QtModel;

class TestTicketModel extends QtModel
{
    public $table = 'tickets';

    public function relations(): array
    {
        return [
            TestUserMeetingModel::class => [
                'foreign_key' => 'meeting_id',
                'local_key' => 'id'
            ]
        ];
    }
}