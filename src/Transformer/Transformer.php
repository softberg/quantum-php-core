<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Transformer;

use Quantum\Transformer\Contracts\TransformerInterface;

/**
 * Class TransformerManager
 * @package Quantum\Transformer
 */
class Transformer
{
    /**
     * Applies the transformer on each item of the array
     * @param array<mixed> $data
     * @return array<mixed>
     */
    public static function transform(array $data, TransformerInterface $transformer): array
    {
        return array_map([$transformer, 'transform'], $data);
    }
}
