<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Libraries\Database\Enums\Relation;
use Quantum\Model\QtModel;

class TestProfileModel extends QtModel
{
    public $table = 'profiles';

    public $idColumn = 'id';

    protected $fillable = [
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
                'local_key' => 'id'
            ],
        ];
    }
}
