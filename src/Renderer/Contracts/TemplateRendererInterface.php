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

namespace Quantum\Renderer\Contracts;

/**
 * Interface TemplateRendererInterface
 * @package Quantum\Renderer
 */
interface TemplateRendererInterface
{
    /**
     * Renders the template
     * @param array<string, mixed> $params
     */
    public function render(string $view, array $params = []): string;
}
