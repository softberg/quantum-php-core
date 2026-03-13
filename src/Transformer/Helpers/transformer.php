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

use Quantum\Transformer\Contracts\TransformerInterface;
use Quantum\Transformer\Transformer;

/**
 * Transforms the data by given transformer signature
 */
function transform(array $data, TransformerInterface $transformer): array
{
    return Transformer::transform($data, $transformer);
}
