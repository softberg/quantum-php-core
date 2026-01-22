<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Libraries\Database\Enums\Relation;
use Quantum\Model\DbModel;

class TestEventModel extends DbModel
{
    public string $table = 'events';

    public string $idColumn = 'id';

    public array $fillable = [
        'title',
        'country',
        'started_at',
    ];

    public function relations(): array
    {
        return [
            TestUserEventModel::class => [
                'type' => Relation::HAS_MANY,
                'foreign_key' => 'event_id',
                'local_key' => 'id',
            ],
        ];
    }
}
