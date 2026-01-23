<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\Traits\SoftDeletes;
use Quantum\Model\DbModel;

class TestProductsModel extends DbModel
{
    use SoftDeletes;

    public string $idColumn = 'id';

    public string $table = 'products';

    public array $fillable = [
        'title',
        'description',
        'price',
        'created_at',
        'deleted_at',
    ];
}
