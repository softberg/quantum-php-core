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

namespace Quantum\Tracer;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\ViewException;
use Quantum\Exceptions\DiException;
use Quantum\Factory\ViewFactory;
use Quantum\Logger\LoggerConfig;
use Quantum\Http\Response;
use Quantum\Logger\Logger;
use ReflectionException;
use Psr\Log\LogLevel;
use ErrorException;
use Quantum\Di\Di;
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
     * @var array
     */
    private $trace = [];

    private static $instance;

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
     * @return void
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
     * @param Throwable $e
     * @return void
     * @throws DiException
     * @throws ReflectionException
     * @throws ViewException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function handleException(Throwable $e): void
    {
        $this->composeStackTrace($e);

        $view = ViewFactory::getInstance();

        $errorType = $this->getErrorType($e);

        if (is_debug_mode()) {
            Response::html($view->renderPartial('errors' . DS . 'trace', [
                'stackTrace' => $this->trace,
                'errorMessage' => $e->getMessage(),
                'severity' => ucfirst($errorType),
            ]));
        } else {
            $this->logError($e, $errorType);
            Response::html($view->renderPartial('errors' . DS . '500'));
        }

        Response::send();
    }

    /**
     * @param Throwable $e
     * @param string $errorType
     * @return void
     */
    private function logError(Throwable $e, string $errorType): void
    {
        if (LoggerConfig::getLogLevel($errorType) >= LoggerConfig::getAppLogLevel()) {
            if (method_exists($this->logger, $errorType)) {
                $this->logger->$errorType($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            } else {
                $this->logger->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }
        }
    }

    /**
     * Composes the stack trace
     * @param Throwable $e
     * @throws DiException
     * @throws ReflectionException
     */
    protected function composeStackTrace(Throwable $e)
    {
        $this->trace[] = [
            'file' => $e->getFile(),
            'code' => $this->getSourceCode($e->getFile(), $e->getLine(), 'error-line'),
        ];

        foreach ($e->getTrace() as $item) {
            if (($item['class'] ?? null) === __CLASS__) {
                continue;
            }

            if (isset($item['file'])) {
                $this->trace[] = [
                    'file' => $item['file'],
                    'code' => $this->getSourceCode($item['file'], $item['line'] ?? 1, 'switch-line'),
                ];
            }
        }
    }

    /**
     * Gets the source code where the error happens
     * @param string $filename
     * @param int $lineNumber
     * @param string $className
     * @return string
     * @throws DiException
     * @throws ReflectionException
     */
    protected function getSourceCode(string $filename, int $lineNumber, string $className): string
    {
        $fs = Di::get(FileSystem::class);

        $start = max($lineNumber - floor(self::NUM_LINES / 2), 1);

        $lines = $fs->getLines($filename, $start, self::NUM_LINES);

        $code = '<ol start="' . key($lines) . '">';
        foreach ($lines as $currentLineNumber => $line) {
            $highlight = $currentLineNumber === $lineNumber ? ' class="' . $className . '"' : '';
            $code .= '<li' . $highlight . '><pre>' . htmlspecialchars($line, ENT_QUOTES) . '</pre></li>';
        }
        $code .= '</ol>';

        return $code;
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