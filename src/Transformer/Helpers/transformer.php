<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 3.0.0
 */

use Quantum\Transformer\Contracts\TransformerInterface;
use Quantum\Transformer\Transformer;

/**
 * Transforms the data by given transformer signature
 * @param array<mixed> $data
 * @return array<mixed>
 */
function transform(array $data, TransformerInterface $transformer): array
{
    return Transformer::transform($data, $transformer);
}
