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
 * @since 2.8.0
 */

namespace Quantum\Libraries\Transformer;

/**
 * Interface TransformerInterface
 * @package Quantum\Libraries\Transformer
 */
interface TransformerInterface
{

    /**
     * Defines the transformer signature
     * @param type $item
     */
    public function transform($item);
}
