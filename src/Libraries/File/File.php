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
 * @since 1.0.0
 */

namespace Quantum\Libraries\File;

use Gumlet\ImageResize;

/**
 * File class
 * 
 * @package Quantum
 * @subpackage Libraries.File
 * @category Libraries
 * @example fileUploadExample.php
 */
class File {

    /**
     * File object
     * 
     * @var object 
     */
    protected $file;
    
    /**
     * ImageResize function name
     * 
     * @var string 
     */
    protected $function;
    
    /**
     *  ImageResize function arguments
     * 
     * @var array 
     */
    protected $params = array();
    
    /**
     *  Alternative name of image
     * 
     * @var string 
     */
    protected $alternaiveName;
    
    /**
     *  Destination folder
     * 
     * @var string 
     */
    protected $dest;
    
    /**
     * Errors list
     * 
     * @var array 
     */
    protected $errors;

    /**
     * Class constructor
     * 
     * @param string $key The $_FILES[] key
     * @param string $dest Destination directory
     * @param bool $overwrite Overwrite existing file 
     * @uses \Upload Codeguy upload
     * @return void
     */
    public function __construct($key, $dest, $overwrite = false) {
        $this->dest = $dest;
        $storage = new \Upload\Storage\FileSystem($dest, $overwrite);
        $this->file = new \Upload\File($key, $storage);
    }

    /**
     * __call magic
     * 
     * Allows to call \Upload\File methods
     * 
     * @param string $method
     * @param mixed $args
     * @uses \Upload\File
     * @return mixed
     */
    public function __call($method, $args = array()) {
        return $this->file->$method(count($args) > 0 ? $args[0] : '');
    }

    /**
     * Add Validations
     * 
     * @param type $params
     * @uses \Upload\Validation 
     * @return void
     */
    public function addValidations($params) {
        $validations = array();

        foreach ($params as $param) {

            switch ($param['type']) {
                case 'mime':
                    array_push($validations, new \Upload\Validation\Mimetype($param['values']));
                    break;
                case 'size':
                    array_push($validations, new \Upload\Validation\Size($param['values']));
                    break;
                case 'dimensions':
                    array_push($validations, new \Upload\Validation\Dimensions($param['values']['width'], $param['values']['height']));
                    break;
            }
        }

        $this->file->addValidations($validations);
    }

    /**
     * Modify
     * 
     * Sets callback function, parameters and alternative name for image modification
     * @return object
     */
    public function modify($function, $params, $altertnaiveName = NULL) {
        $this->function = $function;
        $this->params = $params;
        $this->alternaiveName = $altertnaiveName;

        return $this;
    }

    /**
     * Save
     * 
     * @uses \Upload\File 
     * @uses \ImageResize
     * @return string
     */
    public function save() {
        try {
            $this->file->upload();

            $filename = $this->file->getNameWithExtension();

            if ($this->function) {
                $func = $this->function;
                $fileSource = $this->dest . '/' . $this->file->getNameWithExtension();
                $filename = $this->alternaiveName ? $this->alternaiveName . '.' . $this->file->getExtension() : $this->file->getNameWithExtension();

                $image = new ImageResize($fileSource);
                call_user_func_array(array($image, $func), $this->params);
                $image->save($this->dest . '/' . $filename);
            }
            return $filename;
            
        } catch (\Exception $e) {
            $this->errors = $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * Get Errors
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

}
