<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\Traits\SoftDeletes;
use Quantum\Model\QtModel;

class Products extends QtModel
{

    use SoftDeletes;

    public $idColumn = 'id';

    public $table = 'products';

    public $fillable = [
        'title',
        'description',
        'price',
        'created_at',
        'deleted_at',
    ];
}
