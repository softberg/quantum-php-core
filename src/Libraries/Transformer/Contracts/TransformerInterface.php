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

namespace Quantum\Libraries\Transformer\Contracts;

/**
 * Interface TransformerInterface
 * @package Quantum\Libraries\Transformer
 */
interface TransformerInterface
{
    /**
     * Defines the transformer signature
     * @param mixed $item
     */
    public function transform($item);
}
