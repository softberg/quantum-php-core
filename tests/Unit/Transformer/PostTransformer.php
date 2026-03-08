<?php

namespace Quantum\Tests\Unit\Transformer;

use Quantum\Transformer\Contracts\TransformerInterface;

class PostTransformer implements TransformerInterface
{
    public function transform($item)
    {
        return [
            'id' => $item['id'],
            'title' => $item['title'],
            'content' => $item['content'],
            'updated' => $item['updated_at'],
            'author' => $item['user']['firstname'] . ' ' . $item['user']['lastname'],
        ];
    }
}
