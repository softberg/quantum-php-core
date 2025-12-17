<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Libraries\Database\Enums\Relation;
use Quantum\Model\QtModel;

class TestUserModel extends QtModel
{

    public $idColumn = 'id';

    public $table = 'users';

    public $fillable = [
        'email',
        'password',
    ];

    public $hidden = [
        'password'
    ];

    public function relations(): array
    {
        return [
            TestProfileModel::class => [
                'type' => Relation::HAS_ONE,
                'foreign_key' => 'user_id',
                'local_key' => 'id'
            ],

            TestUserProfessionModel::class => [
                'type' => Relation::HAS_MANY,
                'foreign_key' => 'user_id',
                'local_key' => 'id'
            ],

            TestUserMeetingModel::class => [
                'type' => Relation::HAS_MANY,
                'foreign_key' => 'user_id',
                'local_key' => 'id'
            ],

            TestUserEventModel::class => [
                'type' => Relation::HAS_MANY,
                'foreign_key' => 'user_id',
                'local_key' => 'id'
            ],
        ];
    }
}
