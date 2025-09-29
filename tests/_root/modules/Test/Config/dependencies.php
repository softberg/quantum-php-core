<?php

use Quantum\Tests\_root\modules\Test\Transformers\PostTransformer;
use Quantum\Libraries\Transformer\Contracts\TransformerInterface;
use Quantum\Libraries\Storage\Contracts\TokenServiceInterface;
use Quantum\Tests\_root\shared\Services\TokenService;

return [
    TransformerInterface::class => PostTransformer::class,
    TokenServiceInterface::class => TokenService::class,
];