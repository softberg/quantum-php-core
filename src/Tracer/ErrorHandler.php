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
 * @since 2.9.0
 */

namespace Quantum\Tracer;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\ViewException;
use Quantum\Exceptions\DiException;
use Quantum\Factory\ViewFactory;
use Quantum\Logger\FileLogger;
use Quantum\Http\Response;
use ReflectionException;
use ErrorException;
use Quantum\Di\Di;
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
     * @var array
     */
    private static $trace = [];

    /**
     * @var string[][]
     */
    private static $errorTypes = [
        E_ERROR => ['fn' => 'error', 'severity' => 'Error'],
        E_WARNING => ['fn' => 'warning', 'severity' => 'Warning'],
        E_PARSE => ['fn' => 'error', 'severity' => 'Parsing Error'],
        E_NOTICE => ['fn' => 'notice', 'severity' => 'Notice'],
        E_CORE_ERROR => ['fn' => 'error', 'severity' => 'Core Error'],
        E_CORE_WARNING => ['fn' => 'warning', 'severity' => 'Core Warning'],
        E_COMPILE_ERROR => ['fn' => 'error', 'severity' => 'Compile Error'],
        E_COMPILE_WARNING => ['fn' => 'warning', 'severity' => 'Compile Warning'],
        E_USER_ERROR => ['fn' => 'error', 'severity' => 'User Error'],
        E_USER_WARNING => ['fn' => 'warning', 'severity' => 'User Warning'],
        E_USER_NOTICE => ['fn' => 'notice', 'severity' => 'User Notice'],
        E_STRICT => ['fn' => 'notice', 'severity' => 'Runtime Notice'],
        E_RECOVERABLE_ERROR => ['fn' => 'error', 'severity' => 'Catchable Fatal Error']
    ];

    /**
     * Setups the handlers
     * @throws DiException
     * @throws ErrorException
     * @throws ReflectionException
     * @throws ViewException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function setup()
    {
        set_error_handler(function ($severity, $message, $file, $line) {

            if (!(error_reporting() && $severity)) {
                return;
            }

            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler(function (Throwable $e) {
            self::handle($e);
        });
    }

    /**
     * @param Throwable $e
     * @throws DiException
     * @throws ReflectionException
     * @throws ViewException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected static function handle(Throwable $e)
    {
        self::composeStackTrace($e);

        $fn = 'info';
        $severity = null;

        $errorType = self::getErrorType($e);
 
        if ($errorType) {
            extract($errorType);
        }

        $view = ViewFactory::getInstance();

        if (filter_var(config()->get('debug'), FILTER_VALIDATE_BOOLEAN)) {
            Response::html($view->renderPartial('errors/trace', ['stackTrace' => self::$trace, 'errorMessage' => $e->getMessage(), 'severity' => $severity]));
        } else {
            $logFile = logs_dir() . DS . date('Y-m-d') . '.log';
            $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $severity . ': ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL;

            $fn($logMessage, new FileLogger($logFile));

            Response::html($view->renderPartial('errors/500'));
        }

        Response::send();
        exit;
    }

    /**
     * Composes the stack trace
     * @param Throwable $e
     * @throws DiException
     * @throws ReflectionException
     */
    protected static function composeStackTrace(Throwable $e)
    {
        self::$trace[] = [
            'file' => $e->getFile(),
            'code' => self::getSourceCode($e->getFile(), $e->getLine(), 'error-line')
        ];

        foreach ($e->getTrace() as $item) {
            if (isset($item['class']) && $item['class'] == __CLASS__) {
                continue;
            }

            if (isset($item['file'])) {
                self::$trace[] = [
                    'file' => $item['file'],
                    'code' => self::getSourceCode($item['file'], $item['line'], 'switch-line')
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
    protected static function getSourceCode(string $filename, int $lineNumber, string $className): string
    {
        $fs = Di::get(FileSystem::class);

        $start = max($lineNumber - floor(self::NUM_LINES / 2), 1);

        $lines = $fs->getLines($filename, $start, self::NUM_LINES);

        $code = '<ol start="' . key($lines) . '">';
        foreach ($lines as $currentLineNumber => $line) {
            $code .= '<li ' . ($currentLineNumber == $lineNumber - 1 ? 'class="' . $className . '"' : '') . '><pre>' . $line . '</pre></li>';
        }
        $code .= '</ol>';

        return $code;
    }

    /**
     * Gets the error type
     * @param Throwable $e
     * @return string[]|null
     */
    private static function getErrorType(Throwable $e): ?array
    {
        if ($e instanceof ErrorException) {
            $severity = $e->getSeverity();
            return self::$errorTypes[$severity] ?? null;
        } else if ($e->getCode()) {
            return self::$errorTypes[$e->getCode()] ?? null;
        }

        return null;
    }
}