<?php

namespace Quantum\Tests\Unit\Libraries\Transformer;

use Quantum\Libraries\Transformer\Transformer;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{
    public $posts = [
        [
            'id' => 1,
            'title' => 'Post one title',
            'content' => 'Post one content',
            'updated_at' => '2022-06-29 19:57:00',
            'user' => [
                'id' => 12,
                'firstname' => 'Bob',
                'lastname' => 'Jonson',
                'email' => 'bob@jonson.com',
                'created_at' => '2021-01-12 11:05:00',
            ],
        ],
        [
            'id' => 2,
            'title' => 'Post two title',
            'content' => 'Post two content',
            'updated_at' => '2022-07-21 15:33:00',
            'user' => [
                'id' => 12,
                'firstname' => 'Ken',
                'lastname' => 'Watson',
                'email' => 'ken@watson.com',
                'created_at' => '2021-02-11 12:15:00',
            ],
        ],
    ];
    public $expected = [
        [
            'id' => 1,
            'title' => 'Post one title',
            'content' => 'Post one content',
            'updated' => '2022-06-29 19:57:00',
            'author' => 'Bob Jonson',
        ],
        [
            'id' => 2,
            'title' => 'Post two title',
            'content' => 'Post two content',
            'updated' => '2022-07-21 15:33:00',
            'author' => 'Ken Watson',
        ],
    ];

    public function testDataTransforming()
    {
        $transformed = Transformer::transform($this->posts, new PostTransformer());

        $this->assertEquals($this->expected, $transformed);
    }
}
