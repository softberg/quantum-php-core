<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\DbModel;

class TestPostModel extends DbModel
{
    public string $idColumn = 'id';

    public string $table = 'posts';

    public array $fillable = [
        'title',
        'content',
        'author',
        'published_at',
        'created_at',
    ];
}
