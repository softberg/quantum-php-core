<?php

namespace Quantum\Http\Request;


use Quantum\Exceptions\FileUploadException;

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
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function getFile(string $key)
    {
        if (!self::hasFile($key)) {
            throw new \InvalidArgumentException(_message(FileUploadException::UPLOADED_FILE_NOT_FOUND, $key));
        }
//dd(self::$__files[$key]);
        return self::$__files[$key];
    }

    /**
     * Handles uploaded files
     * @param array $_files
     * @return array
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