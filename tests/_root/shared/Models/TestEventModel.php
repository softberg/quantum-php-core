<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Libraries\Database\Enums\Relation;
use Quantum\Model\QtModel;

class TestEventModel extends QtModel
{
    public $table = 'events';

    public $idColumn = 'id';

    public $fillable = [
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
