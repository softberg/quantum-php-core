<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\QtModel;

class TestNotesModel extends QtModel
{
    public $table = 'notes';

    public $idColumn = 'id';

    public $fillable = [
        'ticket_id',
        'note',
    ];
}
