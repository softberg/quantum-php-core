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

namespace Quantum\Http\Request;

use Quantum\Libraries\Storage\UploadedFile;
use Quantum\Exceptions\FileUploadException;
use Quantum\Exceptions\DiException;
use ReflectionException;

/**
 * Trait File
 * @package Quantum\Http\Request
 */
trait File
{

    /**
     * Files
     * @var array
     */
    private static $__files = [];

    /**
     * Checks to see if request contains file
     * @param string $key
     * @return bool
     */
    public static function hasFile(string $key): bool
    {
        if (!isset(self::$__files[$key])) {
            return false;
        }

        if (!is_array(self::$__files[$key]) && self::$__files[$key]->getErrorCode() != UPLOAD_ERR_OK) {
            return false;
        }

        if (is_array(self::$__files[$key])) {
            foreach (self::$__files[$key] as $file) {
                if ($file->getErrorCode() != UPLOAD_ERR_OK) {
                    return false;
                }
            }

        }

        return true;
    }

    /**
     * Gets the file or array of file objects
     * @param string $key
     * @return mixed
     * @throws FileUploadException
     */
    public static function getFile(string $key)
    {
        if (!self::hasFile($key)) {
            throw FileUploadException::fileNotFound($key);
        }

        return self::$__files[$key];
    }

    /**
     * Handle files
     * @param array $files
     * @return array|UploadedFile[]
     * @throws DiException
     * @throws ReflectionException
     */
    private static function handleFiles(array $files): array
    {
        if (!count($files)) {
            return [];
        }

        $key = key($files);

        if (!$key) {
            return [];
        }

        if (!is_array($files[$key]['name'])) {
            return [$key => new UploadedFile($files[$key])];
        } else {
            $formatted = [];

            foreach ($files[$key]['name'] as $index => $name) {
                $formatted[$key][$index] = new UploadedFile([
                    'name' => $name,
                    'type' => $files[$key]['type'][$index],
                    'tmp_name' => $files[$key]['tmp_name'][$index],
                    'error' => $files[$key]['error'][$index],
                    'size' => $files[$key]['size'][$index],
                ]);
            }

            return $formatted;
        }
    }

}