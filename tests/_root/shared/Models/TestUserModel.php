<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Libraries\Database\Enums\Relation;
use Quantum\Model\DbModel;

class TestUserModel extends DbModel
{
    public string $idColumn = 'id';

    public string $table = 'users';

    public array $fillable = [
        'email',
        'password',
    ];

    public array $hidden = [
        'password',
    ];

    public function relations(): array
    {
        return [
            TestProfileModel::class => [
                'type' => Relation::HAS_ONE,
                'foreign_key' => 'user_id',
                'local_key' => 'id',
            ],

            TestUserProfessionModel::class => [
                'type' => Relation::HAS_MANY,
                'foreign_key' => 'user_id',
                'local_key' => 'id',
            ],

            TestUserMeetingModel::class => [
                'type' => Relation::HAS_MANY,
                'foreign_key' => 'user_id',
                'local_key' => 'id',
            ],

            TestUserEventModel::class => [
                'type' => Relation::HAS_MANY,
                'foreign_key' => 'user_id',
                'local_key' => 'id',
            ],
        ];
    }
}
