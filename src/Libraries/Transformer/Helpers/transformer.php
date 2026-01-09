<?php

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

use Quantum\Libraries\Transformer\Contracts\TransformerInterface;
use Quantum\Libraries\Transformer\Transformer;

/**
 * Transforms the data by given transformer signature
 * @param array $data
 * @param TransformerInterface $transformer
 * @return array
 */
function transform(array $data, TransformerInterface $transformer): array
{
    return Transformer::transform($data, $transformer);
}
