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

namespace Quantum\Libraries\Validation\Rules;

use Quantum\Libraries\Upload\File as FileUpload;

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
     * @param \Quantum\Libraries\Upload\File $file
     * @param mixed $param
     */
    protected function fileSize(string $field, FileUpload $file, $param)
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
     * @param \Quantum\Libraries\Upload\File $file
     * @param mixed $param
     */
    protected function fileMimeType(string $field, FileUpload $file, $param)
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
     * @param \Quantum\Libraries\Upload\File $file
     * @param mixed $param
     */
    protected function fileExtension(string $field, FileUpload $file, $param)
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
     * @param \Quantum\Libraries\Upload\File $file
     * @param array $param
     */
    protected function imageDimensions(string $field, FileUpload $file, array $param)
    {
        $dimensions = $file->getDimensions();

        if (!empty($dimensions)) {
            if ($dimensions['width'] != $param[0] || $dimensions['height'] != $param[1]) {
                $this->addError($field, 'imageDimensions', $param);
            }
        }
    }

}