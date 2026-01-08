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
 * @since 3.0.0
 */

use Symfony\Component\VarExporter\Exception\ExceptionInterface;
use Symfony\Component\VarExporter\VarExporter;

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

    return $decoded !== false && base64_encode($decoded) === $string;
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
        $randomString .= random_int(0, 9);
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

    if ($text === '' || $text === '0') {
        return 'n-a';
    }

    return $text;
}

/**
 * Checks the app debug mode
 * @return bool
 */
function is_debug_mode(): bool
{
    return filter_var(config()->get('app.debug'), FILTER_VALIDATE_BOOLEAN);
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
    $trace = debug_backtrace();

    if (!isset($trace[$index])) {
        return null;
    }

    return $trace[$index]['function'];
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