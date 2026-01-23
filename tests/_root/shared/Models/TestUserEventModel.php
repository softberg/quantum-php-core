<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Libraries\Database\Enums\Relation;
use Quantum\Model\DbModel;

class TestUserEventModel extends DbModel
{
    public string $table = 'user_events';

    public string $idColumn = 'id';

    public array $fillable = [
        'user_id',
        'event_id',
        'confirmed',
        'created_at',
    ];

    public function relations(): array
    {
        return [
            TestEventModel::class => [
                'type' => Relation::BELONGS_TO,
                'foreign_key' => 'event_id',
                'local_key' => 'id',
            ],
        ];
    }
}
