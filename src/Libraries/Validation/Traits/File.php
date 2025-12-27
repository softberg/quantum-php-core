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
 * @since 2.9.8
 */

namespace Quantum\Libraries\Validation\Traits;

use Quantum\Libraries\Storage\Exceptions\FileUploadException;
use Quantum\Libraries\Storage\UploadedFile;

/**
 * Trait File
 * @package Quantum\Libraries\Validation\Rules
 */
trait File
{

    /**
     * Validates file size
     * @param UploadedFile $file
     * @param int|null $maxSize
     * @param int|null $minSize
     * @return bool
     */
    public function fileSize(UploadedFile $file, int $maxSize, ?int $minSize = null): bool
    {
        $size = $file->getSize();
        $minSize = $minSize ?? 0;

        return $size >= $minSize && $size <= $maxSize;
    }

    /**
     * Validates file mime type
     * @param UploadedFile $file
     * @param string ...$mimeTypes
     * @return bool
     */
    public function fileMimeType(UploadedFile $file, string ...$mimeTypes): bool
    {
        return in_array($file->getMimetype(), $mimeTypes);
    }

    /**
     * Validates file extension
     * @param UploadedFile $file
     * @param string ...$extensions
     * @return bool
     */
    public function fileExtension(UploadedFile $file, string ...$extensions): bool
    {
        return in_array($file->getExtension(), $extensions);
    }

    /**
     * Validates image dimensions
     * @param UploadedFile $file
     * @param int|null $width
     * @param int|null $height
     * @return bool
     * @throws FileUploadException
     */
    public function imageDimensions(UploadedFile $file, ?int $width = null, ?int $height = null): bool
    {
        $dimensions = $file->getDimensions();

        if ($dimensions === []) {
            return true;
        }

        if ($width !== null && $dimensions['width'] != $width) {
            return false;
        }

        if ($height !== null && $dimensions['height'] != $height) {
            return false;
        }

        return true;
    }
}