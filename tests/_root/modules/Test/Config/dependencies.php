<?php

return [
    \Quantum\Libraries\Transformer\Transformer::class => \Shared\Transformers\PostTransformer::class,
    \Quantum\Service\QtService::class => \Shared\Services\TokenService::class,
];