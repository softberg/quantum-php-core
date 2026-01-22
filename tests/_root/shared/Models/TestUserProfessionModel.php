<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\DbModel;

class TestUserProfessionModel extends DbModel
{
    public string $table = 'user_professions';

    public string $idColumn = 'id';

    public array $fillable = [
        'user_id',
        'title',
    ];
}
