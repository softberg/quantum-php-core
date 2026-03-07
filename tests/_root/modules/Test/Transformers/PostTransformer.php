<?php

namespace Quantum\Tests\_root\modules\Test\Transformers;

use Quantum\Transformer\Contracts\TransformerInterface;

class PostTransformer implements TransformerInterface
{
    public function transform($item): array
    {
        return [
            'uuid' => $item->uuid,
            'title' => $item->title,
            'content' => markdown_to_html($item->content, true),
            'image' => $item->image ? $item->user_directory . '/' . $item->image : null,
            'date' => date('Y/m/d H:i', strtotime($item->updated_at)),
            'author' => $item->firstname . ' ' . $item->lastname,
        ];
    }
}
