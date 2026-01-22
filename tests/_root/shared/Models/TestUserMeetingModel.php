<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Libraries\Database\Enums\Relation;
use Quantum\Model\DbModel;

class TestUserMeetingModel extends DbModel
{
    public string $table = 'user_meetings';

    public string $idColumn = 'id';

    public array $fillable = [
        'user_id',
        'title',
        'start_date',
    ];

    public function relations(): array
    {
        return [
            TestTicketModel::class => [
                'type' => Relation::HAS_MANY,
                'foreign_key' => 'meeting_id',
                'local_key' => 'id',
            ],
        ];

    }
}
