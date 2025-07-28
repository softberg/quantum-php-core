<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\QtModel;

class TestNotesModel extends QtModel
{
    public $table = 'notes';

    public function relations(): array
    {
        return [
            TestTicketModel::class => [
                'foreign_key' => 'ticket_id',
                'local_key' => 'id'
            ]
        ];
    }
}