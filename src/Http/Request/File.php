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
 * @since 2.4.0
 */

namespace Quantum\Http\Request;

use Quantum\Exceptions\FileUploadException;

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
        return isset(self::$__files[$key]);
    }

    /**
     * Gets the file info by given key
     * @param string $key
     * @return array|object
     * @throws \InvalidArgumentException
     */
    public static function getFile(string $key)
    {
        if (!self::hasFile($key)) {
            throw new \InvalidArgumentException(_message(FileUploadException::UPLOADED_FILE_NOT_FOUND, $key));
        }

        return self::$__files[$key];
    }

    /**
     * @param array $_files
     * @return array|object[]
     */
    private static function handleFiles(array $_files): array
    {
        if (!count($_files)) {
            return [];
        }

        $key = key($_files);

        if ($key) {
            if (!is_array($_files[$key]['name'])) {
                return [$key => (object)$_files[$key]];
            } else {
                $formattedFiles = [];

                foreach ($_files[$key]['name'] as $index => $name) {
                    $formattedFiles[$key][$index] = (object)[
                        'name' => $name,
                        'type' => $_files[$key]['type'][$index],
                        'tmp_name' => $_files[$key]['tmp_name'][$index],
                        'error' => $_files[$key]['error'][$index],
                        'size' => $_files[$key]['size'][$index],
                    ];
                }

                return $formattedFiles;
            }
        }

    }

}