<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace {{MODULE_NAMESPACE}}\Models;

use Quantum\Database\Enums\Relation;
use Quantum\Model\Traits\HasTimestamps;
use Quantum\Model\Traits\SoftDeletes;
use Quantum\Model\DbModel;

/**
 * Class Post
 * @package Modules\{{MODULE_NAME}}
 */
class Post extends DbModel
{

    use HasTimestamps;
    use SoftDeletes;

    /**
     * ID column of table
     */
    public string $idColumn = 'id';

    /**
     * The table name
     */
    public string $table = 'posts';

    /**
     * Fillable properties
     */
    public array $fillable = [
        'uuid',
        'user_uuid',
        'title',
        'content',
        'image',
    ];

    /**
     * Model relations configuration
     */
    public function relations(): array
    {
        return [
            User::class => [
                'type' => Relation::BELONGS_TO,
                'foreign_key' => 'user_uuid',
                'local_key' => 'uuid',
            ]
        ];
    }
}
