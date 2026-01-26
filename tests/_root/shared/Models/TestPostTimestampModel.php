<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\Traits\HasTimestamps;
use Quantum\Model\DbModel;

class TestPostTimestampModel extends DbModel
{
    use HasTimestamps;

    public string $idColumn = 'id';

    public string $table = 'posts';

    protected array $fillable = [
        'title',
        'content',
        'author',
        'published_at',
    ];
}
