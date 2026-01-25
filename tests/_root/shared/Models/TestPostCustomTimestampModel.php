<?php

namespace Quantum\Tests\_root\shared\Models;

use Quantum\Model\Traits\HasTimestamps;
use Quantum\Model\DbModel;

class TestPostCustomTimestampModel extends DbModel
{
    use HasTimestamps;

    public const CREATED_AT = 'created_on';

    public const UPDATED_AT = 'modified_on';

    public string $table = 'posts_custom';

    protected array $fillable = [
        'title',
        'content',
        'author',
        'published_at',
    ];
}
