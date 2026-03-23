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

namespace Quantum\Renderer\Enums;

/**
 * Class RendererType
 * @package Quantum\Renderer
 */
final class RendererType
{
    public const HTML = 'html';

    public const TWIG = 'twig';

    private function __construct()
    {
    }
}
