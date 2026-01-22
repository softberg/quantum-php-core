<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Libraries\Database\Enums\Relation;
use Quantum\Model\DbModel;

class TestTicketModel extends DbModel
{
    public string $table = 'tickets';

    public string $idColumn = 'id';

    public array $fillable = [
        'meeting_id',
        'type',
        'number',
    ];

    public function relations(): array
    {
        return [
            TestNotesModel::class => [
                'type' => Relation::HAS_MANY,
                'foreign_key' => 'ticket_id',
                'local_key' => 'id',
            ],
        ];
    }
}
