<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\QtModel;

class TestUserProfessionModel extends QtModel
{

    public $table = 'user_professions';

    public function relations(): array
    {
        return [
            TestUserModel::class => [
                'foreign_key' => 'user_id',
                'local_key' => 'id'
            ]
        ];
    }
}
