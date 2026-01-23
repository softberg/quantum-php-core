<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\DbModel;

class TestNotesModel extends DbModel
{
    public string $table = 'notes';

    public string $idColumn = 'id';

    public array $fillable = [
        'ticket_id',
        'note',
    ];
}
