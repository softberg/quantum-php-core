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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Transformer;

use Quantum\Libraries\Transformer\Contracts\TransformerInterface;

/**
 * Class TransformerManager
 * @package Quantum\Libraries\Transformer
 */
class Transformer
{

    /**
     * Applies the transformer on each item of the array
     * @param array $data
     * @param TransformerInterface $transformer
     * @return array
     */
    public static function transform(array $data, TransformerInterface $transformer): array
    {
        return array_map([$transformer, 'transform'], $data);
    }
}