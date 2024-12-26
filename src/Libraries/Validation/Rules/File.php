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

namespace Quantum\Libraries\Validation\Rules;

use Quantum\Exceptions\FileUploadException;
use Quantum\Libraries\Storage\UploadedFile;

/**
 * Trait File
 * @package Quantum\Libraries\Validation\Rules
 */
trait File
{

    /**
     * Adds validation Error
     * @param string $field
     * @param string $rule
     * @param mixed|null $param
     */
    abstract protected function addError(string $field, string $rule, $param = null);

    /**
     * Validates file size
     * @param string $field
     * @param UploadedFile $file
     * @param $param
     */
    protected function fileSize(string $field, UploadedFile $file, $param)
    {
        if (!is_array($param)) {
            if ($file->getSize() > $param) {
                $this->addError($field, 'fileSize', $param);
            }
        } else {
            if ($file->getSize() < $param[0] || $file->getSize() > $param[1]) {
                $this->addError($field, 'fileSize', $param);
            }
        }
    }

    /**
     * Validates file mime type
     * @param string $field
     * @param UploadedFile $file
     * @param $param
     */
    protected function fileMimeType(string $field, UploadedFile $file, $param)
    {
        if (!is_array($param)) {
            if ($file->getMimetype() != $param) {
                $this->addError($field, 'fileMimeType', $param);
            }
        } else {
            if (!in_array($file->getMimetype(), $param)) {
                $this->addError($field, 'fileMimeType', $param);
            }
        }
    }

    /**
     * Validates file extension
     * @param string $field
     * @param UploadedFile $file
     * @param $param
     * @return void
     */
    protected function fileExtension(string $field, UploadedFile $file, $param)
    {
        if (!is_array($param)) {
            if ($file->getExtension() != $param) {
                $this->addError($field, 'fileExtension', $param);
            }
        } else {
            if (!in_array($file->getExtension(), $param)) {
                $this->addError($field, 'fileExtension', $param);
            }
        }
    }

    /**
     * Validates image dimensions
     * @param string $field
     * @param UploadedFile $file
     * @param array $param
     * @throws FileUploadException
     */
    protected function imageDimensions(string $field, UploadedFile $file, array $param)
    {
        $dimensions = $file->getDimensions();

        if (!empty($dimensions)) {
            if ($dimensions['width'] != $param[0] || $dimensions['height'] != $param[1]) {
                $this->addError($field, 'imageDimensions', $param);
            }
        }
    }

}