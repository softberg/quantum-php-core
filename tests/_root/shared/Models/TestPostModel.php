<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\QtModel;

class TestPostModel extends QtModel
{
    public $idColumn = 'id';

    public $table = 'posts';

    public $fillable = [
        'title',
        'content',
        'author',
        'published_at',
        'created_at',
    ];
}
