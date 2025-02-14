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

namespace Quantum\Renderer\Contracts;

/**
 * Interface TemplateRendererInterface
 * @package Quantum\Renderer
 */
interface TemplateRendererInterface
{
    /**
     * Renders the template
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;
}