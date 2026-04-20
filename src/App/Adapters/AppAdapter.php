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

namespace Quantum\App\Adapters;

use Quantum\App\Contracts\AppInterface;
use Quantum\App\AppContext;

/**
 * Class AppAdapter
 * @package Quantum\App
 */
abstract class AppAdapter implements AppInterface
{
    protected AppContext $context;

    public function __construct(AppContext $context)
    {
        $this->context = $context;
    }
}
