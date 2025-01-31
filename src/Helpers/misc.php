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

use Symfony\Component\VarExporter\Exception\ExceptionInterface;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Renderer\Exceptions\RendererException;
use Quantum\Exceptions\StopExecutionException;
use Symfony\Component\VarExporter\VarExporter;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Http\Response;

/**
 * Compose a message
 * @param string $subject
 * @param string|array $params
 * @return string
 */
function _message(string $subject, $params): string
{
    if (is_array($params)) {
        return preg_replace_callback('/{%\d+}/', function () use (&$params) {
            return array_shift($params);
        }, $subject);
    } else {
        return preg_replace('/{%\d+}/', $params, $subject);
    }
}

/**
 * Validates base64 string
 * @param string $string
 * @return boolean
 */
function valid_base64(string $string): bool
{
    if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) {
        return false;
    }

    $decoded = base64_decode($string, true);

    if (!base64_decode($string, true)) {
        return false;
    }

    if (base64_encode($decoded) != $string) {
        return false;
    }

    return true;
}

/**
 * Generates random number sequence
 * @param int $length
 * @return int
 */
function random_number(int $length = 10): int
{
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= rand(0, 9);
    }
    return (int)$randomString;
}

/**
 * Slugify the string
 * @param string $text
 * @return string
 */
function slugify(string $text): string
{
    $text = trim($text, ' ');
    $text = preg_replace('/[^\p{L}\p{N}]/u', ' ', $text);
    $text = preg_replace('/\s+/', '-', $text);
    $text = trim($text, '-');
    $text = mb_strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

/**
 * Stops the execution
 * @param Closure|null $closure
 * @throws StopExecutionException
 */
function stop(Closure $closure = null, ?int $code = 0)
{
    if ($closure) {
        $closure();
    }

    throw StopExecutionException::executionTerminated($code);
}

/**
 * Checks the app debug mode
 * @return bool
 */
function is_debug_mode(): bool
{
    return filter_var(config()->get('debug'), FILTER_VALIDATE_BOOLEAN);
}

/**
 * Handles page not found
 * @return void
 * @throws DiException
 * @throws ReflectionException
 * @throws BaseException
 * @throws ConfigException
 * @throws RendererException
 */
function page_not_found()
{
    $acceptHeader = Response::getHeader('Accept');

    $isJson = $acceptHeader === 'application/json';

    if ($isJson) {
        Response::json(
            ['status' => 'error', 'message' => 'Page not found'],
            404
        );
    } else {
        Response::html(
            partial('errors' . DS . '404'),
            404
        );
    }
}

/**
 * Gets directory classes
 * @param string $path
 * @return array
 */
function get_directory_classes(string $path): array
{
    $class_names = [];

    if (is_dir($path)) {
        $allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $phpFiles = new RegexIterator($allFiles, '/\.php$/');

        foreach ($phpFiles as $file) {
            $class = pathinfo($file->getFilename());
            $class_names[] = $class['filename'];
        }
    }

    return $class_names;
}

/**
 * Gets the caller class
 * @param integer $index
 * @return string|null
 */
function get_caller_class(int $index = 2): ?string
{
    $caller = debug_backtrace();
    $caller = $caller[$index];

    return $caller['class'] ?? null;
}

/**
 * Gets the caller function
 * @param integer $index
 * @return string|null
 */
function get_caller_function(int $index = 2): ?string
{
    $caller = debug_backtrace();
    $caller = $caller[$index];

    return $caller['function'] ?? null;
}

/**
 * Exports the variable
 * @param $var
 * @return string
 * @throws ExceptionInterface
 */
function export($var): string
{
    return VarExporter::export($var);
}

/**
 * Checks if the entity is closure
 * @param mixed $entity
 * @return bool
 */
function is_closure($entity): bool
{
    return $entity instanceof Closure;
}