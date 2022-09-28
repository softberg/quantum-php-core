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
 * @since 2.8.0
 */

use Quantum\Libraries\Transformer\TransformerInterface;
use Quantum\Libraries\Transformer\TransformerManager;
use Quantum\Exceptions\StopExecutionException;
use Quantum\Libraries\Asset\AssetManager;
use Quantum\Exceptions\AppException;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Libraries\Storage\FileSystem;

/**
 * Generates the CSRF token
 * @return string|null
 * @throws \Quantum\Exceptions\AppException
 * @throws \Quantum\Exceptions\DatabaseException
 * @throws \Quantum\Exceptions\SessionException
 */
function csrf_token(): ?string
{
    $appKey = env('APP_KEY');

    if (!$appKey) {
        throw AppException::missingAppKey();
    }

    return Csrf::generateToken(session(), $appKey);
}

/**
 * _message
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
    $decoded = base64_decode($string, true);

    // Check if there is no invalid character in string
    if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) {
        return false;
    }

    // Decode the string in strict mode and send the response
    if (!base64_decode($string, true)) {
        return false;
    }

    // Encode and compare it to original one
    if (base64_encode($decoded) != $string) {
        return false;
    }

    return true;
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
            array_push($class_names, $class['filename']);
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
 *
 * @throws \Quantum\Exceptions\StopExecutionException
 */

/**
 * Stops the execution
 * @param \Closure|null $closure
 * @throws \Quantum\Exceptions\StopExecutionException
 */
function stop(Closure $closure = null)
{
    if ($closure) {
        $closure();
    }

    throw StopExecutionException::executionTerminated();
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
    return (int) $randomString;
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
 * Dumps the assets
 * @param string $type
 * @throws \Quantum\Exceptions\AssetException
 */
function assets(string $type)
{
    $assetTypes = [
        'css' => 1,
        'js' => 2
    ];

    AssetManager::getInstance()->dump($assetTypes[$type]);
}

/**
 * Checks if the entity is closure
 * @param mixed $entity
 * @return bool
 */
function is_closure($entity): bool
{
    return $entity instanceof \Closure;
}

/**
 * Transforms the data by given transformer signature
 * @param array $data
 * @param TransformerInterface $transformer
 * @return array
 */
function transform(array $data, TransformerInterface $transformer): array
{
    return TransformerManager::transform($data, $transformer);
}

/**
 * Checks if already installed
 * @return bool
 */
function installed(string $path): bool
{
    $fs = new FileSystem();

    if ($fs->exists($path)) {
        return true;
    }

    return false;
}
