<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Libraries\Database\Enums\Relation;
use Quantum\Model\DbModel;

class TestProfileModel extends DbModel
{
    public string $table = 'profiles';

    public string $idColumn = 'id';

    protected array $fillable = [
        'user_id',
        'firstname',
        'lastname',
        'age',
        'country',
        'created_at',
    ];

    public function relations(): array
    {
        return [
            TestUserModel::class => [
                'type' => Relation::BELONGS_TO,
                'foreign_key' => 'user_id',
                'local_key' => 'id',
            ],
        ];
    }
}
