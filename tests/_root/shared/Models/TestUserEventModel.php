<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Libraries\Database\Enums\Relation;
use Quantum\Model\QtModel;

class TestUserEventModel extends QtModel
{
    public $table = 'user_events';

    public $idColumn = 'id';

    public $fillable = [
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
                'local_key' => 'id'
            ]
        ];
    }
}
