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
 * @since 2.6.0
 */

namespace Quantum\Renderer;

interface TemplateRenderer
{
    /**
     * Renders the template
     * @param string $view
     * @param array $params
     * @param array $configs
     * @return string
     */
    public function render(string $view, array $params = [], array $configs = []): string;
}