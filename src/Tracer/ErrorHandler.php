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
 * @since 2.9.7
 */

namespace Quantum\Tracer;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Quantum\Renderer\Exceptions\RendererException;
use Quantum\Di\Exceptions\DiException;
use Quantum\View\Factories\ViewFactory;
use Quantum\Exceptions\BaseException;
use Quantum\Libraries\Logger\Logger;
use DebugBar\DebugBarException;
use Quantum\Http\Response;
use ReflectionException;
use Psr\Log\LogLevel;
use ErrorException;
use ParseError;
use Throwable;

/**
 * Class ErrorHandler
 * @package Quantum\Tracer
 */
class ErrorHandler
{
    /**
     * Number of lines to be returned
     */
    const NUM_LINES = 10;

    /**
     * @var string[][]
     */
    const ERROR_TYPES = [
        E_ERROR => 'error',
        E_WARNING => 'warning',
        E_PARSE => 'error',
        E_NOTICE => 'notice',
        E_CORE_ERROR => 'error',
        E_CORE_WARNING => 'warning',
        E_COMPILE_ERROR => 'error',
        E_COMPILE_WARNING => 'warning',
        E_USER_ERROR => 'error',
        E_USER_WARNING => 'warning',
        E_USER_NOTICE => 'notice',
        E_STRICT => 'notice',
        E_RECOVERABLE_ERROR => 'error',
    ];

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ErrorHandler|null
     */
    private static $instance = null;

    private function __construct()
    {
        // Prevent direct instantiation
    }

    private function __clone()
    {
        // Prevent cloning
    }

    /**
     * @return ErrorHandler
     */
    public static function getInstance(): ErrorHandler
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param Logger $logger
     */
    public function setup(Logger $logger)
    {
        $this->logger = $logger;

        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    /**
     * @param $severity
     * @param $message
     * @param $file
     * @param $line
     * @throws ErrorException
     */
    public function handleError($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            return;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * @param Throwable $throwable
     * @return void
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     * @throws RendererException
     */
    public function handleException(Throwable $throwable): void
    {
        if (PHP_SAPI === 'cli') {
            $this->handleCliException($throwable);
        } else {
            $this->handleWebException($throwable);
        }
    }

    /**
     * @param Throwable $throwable
     * @return void
     */
    private function handleCliException(Throwable $throwable): void
    {
        $output = new ConsoleOutput();
        $output->writeln("<error>" . $throwable->getMessage() . "</error>");
    }

    /**
     * @param Throwable $throwable
     * @return void
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     * @throws DebugBarException
     */
    private function handleWebException(Throwable $throwable): void
    {
        $view = ViewFactory::get();
        $errorType = $this->getErrorType($throwable);

        if (is_debug_mode()) {
            $errorPage = $view->renderPartial('errors' . DS . 'trace', [
                'stackTrace' => $this->composeStackTrace($throwable),
                'errorMessage' => $throwable->getMessage(),
                'severity' => ucfirst($errorType),
            ]);
        } else {
            $errorPage = $view->renderPartial('errors' . DS . '500');
            $this->logError($throwable, $errorType);
        }

        Response::html($errorPage);
        Response::send();
    }


    /**
     * @param Throwable $e
     * @param string $errorType
     * @return void
     */
    private function logError(Throwable $e, string $errorType): void
    {
        $logMethod = method_exists($this->logger, $errorType) ? $errorType : 'error';

        $this->logger->$logMethod($e->getMessage(), ['trace' => $e->getTraceAsString()]);
    }

    /**
     * Composes the stack trace
     * @param Throwable $e
     * @return array
     * @throws BaseException
     */
    private function composeStackTrace(Throwable $e): array
    {
        $trace[] = [
            'file' => $e->getFile(),
            'code' => $this->getSourceCode($e->getFile(), $e->getLine(), 'error-line'),
        ];

        foreach ($e->getTrace() as $item) {
            if (($item['class'] ?? null) === __CLASS__) {
                continue;
            }

            if (isset($item['file'])) {
                $trace[] = [
                    'file' => $item['file'],
                    'code' => $this->getSourceCode($item['file'], $item['line'] ?? 1, 'switch-line'),
                ];
            }
        }

        return $trace;
    }

    /**
     * Gets the source code where the error happens
     * @param string $filename
     * @param int $lineNumber
     * @param string $className
     * @return string
     * @throws BaseException
     */
    private function getSourceCode(string $filename, int $lineNumber, string $className): string
    {
        $fs = FileSystemFactory::get();

        $lineNumber--;

        $start = max($lineNumber - floor(self::NUM_LINES / 2), 1);

        $lines = $fs->getLines($filename, $start, self::NUM_LINES);

        $code = '<ol start="' . key($lines) . '">';

        foreach ($lines as $currentLineNumber => $line) {
            $code .= $this->formatLineItem($currentLineNumber, $line, $lineNumber, $className);
        }

        $code .= '</ol>';

        return $code;
    }

    /**
     * Formats the line item
     * @param int $currentLineNumber
     * @param string $line
     * @param int $lineNumber
     * @param string $className
     * @return string
     */
    private function formatLineItem(int $currentLineNumber, string $line, int $lineNumber, string $className): string
    {
        $highlightClass = $currentLineNumber === $lineNumber ? " class=\"{$className}\"" : '';

        $encodedLine = htmlspecialchars($line, ENT_QUOTES);

        return sprintf(
            '<li%s><pre>%s</pre></li>',
            $highlightClass,
            $encodedLine
        );
    }

    /**
     * Gets the error type based on the exception class
     * @param Throwable $e
     * @return string
     */
    private function getErrorType(Throwable $e): string
    {
        if ($e instanceof ErrorException) {
            return self::ERROR_TYPES[$e->getSeverity()] ?? LogLevel::ERROR;
        }

        if ($e instanceof ParseError) {
            return LogLevel::CRITICAL;
        }

        if ($e instanceof ReflectionException) {
            return LogLevel::WARNING;
        }

        return LogLevel::ERROR;
    }
}