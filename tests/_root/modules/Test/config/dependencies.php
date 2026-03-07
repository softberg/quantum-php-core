<?php

use Quantum\Tests\_root\modules\Test\Transformers\PostTransformer;
use Quantum\Transformer\Contracts\TransformerInterface;
use Quantum\Tests\_root\shared\Services\TokenService;
use Quantum\Storage\Contracts\TokenServiceInterface;

return [
    TransformerInterface::class => PostTransformer::class,
    TokenServiceInterface::class => TokenService::class,
];
