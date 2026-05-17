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
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Tracer;

use Quantum\Renderer\Exceptions\RendererException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use ReflectionException;
use Throwable;

class WebExceptionRenderer
{
    private StackTraceFormatter $stackTraceFormatter;

    public function __construct(?StackTraceFormatter $stackTraceFormatter = null)
    {
        $this->stackTraceFormatter = $stackTraceFormatter ?? new StackTraceFormatter();
    }

    /**
     * @throws ConfigException|RendererException|DiException|BaseException|ReflectionException
     */
    public function render(Throwable $throwable, string $errorType): string
    {
        if (is_debug_mode()) {
            return view()->renderPartial('errors' . DS . 'trace', [
                'stackTrace' => $this->stackTraceFormatter->compose($throwable),
                'errorMessage' => $throwable->getMessage(),
                'severity' => ucfirst($errorType),
            ]);
        }

        return view()->renderPartial('errors' . DS . '500');
    }
}
