<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\QtModel;

class TestUserProfessionModel extends QtModel
{

    public $table = 'user_professions';

    public $primaryKey = 'id';

    public $fillable = [
        'user_id',
        'title'
    ];
}
