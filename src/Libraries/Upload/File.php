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
 * @since 2.6.0
 */

namespace Quantum\Libraries\Upload;

use Quantum\Exceptions\FileUploadException;
use Quantum\Exceptions\FileSystemException;
use Quantum\Libraries\Storage\FileSystem;
use Gumlet\ImageResize;
use Quantum\Di\Di;
use SplFileInfo;
use finfo;

/**
 * Class File
 * @package Quantum\Libraries\Upload
 */
class File extends SplFileInfo
{

    /**
     * File System
     * @var FileSystem
     */
    protected $fs;

    /**
     * Original file name provided by client
     * @var string
     */
    protected $originalName = null;

    /**
     * File name (without extension)
     * @var string
     */
    protected $name = null;

    /**
     * File extension
     * @var string
     */
    protected $extension = null;

    /**
     * File mime type
     * @var string
     */
    protected $mimetype = null;

    /**
     * ImageResize function name
     * @var string
     */
    protected $funcName = null;

    /**
     * ImageResize function arguments
     * @var array
     */
    protected $params = [];

    /**
     * Upload error code messages
     * @var array
     */
    protected $errorMessages = [
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload'
    ];

    /**
     * Blacklisted extensions
     * @var array
     */
    protected $blacklistedExtensions = [
        'php([0-9])?', 'pht', 'phar', 'phpt', 'pgif', 'phtml', 'phtm', 'phps',
        'cgi', 'inc', 'env', 'htaccess', 'htpasswd', 'config', 'conf',
        'bat', 'exe', 'msi', 'cmd', 'dll', 'sh', 'com', 'app', 'sys', 'drv',
        'pl', 'jar', 'jsp', 'js', 'vb', 'vbscript', 'wsf', 'asp', 'py',
        'cer', 'csr', 'crt',
    ];

    /**
     * Upload error code
     * @var int
     */
    protected $errorCode;

    /**
     * File constructor.
     * @param array $file
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    public function __construct(array $file)
    {
        $this->fs = Di::get(FileSystem::class);

        $this->originalName = $file['name'];
        $this->errorCode = $file['error'];

        parent::__construct($file['tmp_name']);
    }

    /**
     * Get name
     * @return string
     */
    public function getName(): string
    {
        if (!$this->name) {
            $this->name = $this->fs->fileName($this->originalName);
        }

        return $this->name;
    }

    /**
     * Set name (without extension)
     * @param string $name
     * @return $this
     */
    public function setName(string $name): File
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get file extension (without leading dot)
     * @return string
     */
    public function getExtension(): string
    {
        if (!$this->extension) {
            $this->extension = strtolower($this->fs->extension($this->originalName));
        }

        return $this->extension;
    }

    /**
     * Get file name with extension
     * @return string
     */
    public function getNameWithExtension(): string
    {
        return $this->getName() . '.' . $this->getExtension();
    }

    /**
     * Get mime type
     * @return string
     */
    public function getMimeType(): string
    {
        if (!$this->mimetype) {
            $finfo = new finfo(FILEINFO_MIME);
            $mimetype = $finfo->file($this->getPathname());
            $mimetypeParts = preg_split('/\s*[;,]\s*/', $mimetype);
            $this->mimetype = strtolower($mimetypeParts[0]);
            unset($finfo);
        }

        return $this->mimetype;
    }

    /**
     * Get md5
     * @return string
     */
    public function getMd5(): string
    {
        return md5_file($this->getPathname());
    }

    /**
     * Get image dimensions
     * @return array
     */
    public function getDimensions(): array
    {
        list($width, $height) = getimagesize($this->getPathname());

        return [
            'width' => $width,
            'height' => $height
        ];
    }

    /**
     * Save the uploaded file
     * @param string $dest
     * @param bool $overwrite
     * @return bool
     * @throws \Gumlet\ImageResizeException
     * @throws \Quantum\Exceptions\FileUploadException
     */
    public function save(string $dest, bool $overwrite = false): bool
    {
        if ($this->errorCode !== UPLOAD_ERR_OK) {
            throw new FileUploadException($this->getErrorMessage());
        }

        if (!$this->whitelisted($this->getExtension())) {
            throw FileUploadException::fileTypeNotAllowed($this->getExtension());
        }

        if (!$this->fs->isDirectory($dest)) {
            throw FileSystemException::directoryNotExists($dest);
        }

        if (!$this->fs->isWritable($dest)) {
            throw FileSystemException::directoryNotWritable($dest);
        }

        $filePath = $dest . DS . $this->getNameWithExtension();

        if ($overwrite === false && $this->fs->exists($filePath)) {
            throw FileSystemException::fileAlreadyExists();
        }

        if (!$this->moveUploadedFile($filePath)) {
            return false;
        }

        if ($this->funcName) {
            $image = new ImageResize($filePath);
            call_user_func_array([$image, $this->funcName], $this->params);

            $image->save($filePath);
        }

        return true;
    }

    /**
     * Applies modification on file
     * @param string $funcName
     * @param array $params
     * @return self
     */
    public function modify(string $funcName, array $params): File
    {
        $this->funcName = $funcName;
        $this->params = $params;

        return $this;
    }

    /**
     * Gets the error code
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * Gets the error message from code
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessages[$this->errorCode];
    }

    /**
     * Tells whether the file was uploaded
     * @return bool
     */
    public function isUploaded(): bool
    {
        return is_uploaded_file($this->getPathname());
    }

    /**
     * Moves an uploaded file to a new location
     * @param string $filePath
     * @return bool
     */
    protected function moveUploadedFile(string $filePath): bool
    {
        if ($this->isUploaded()) {
            return move_uploaded_file($this->getPathname(), $filePath);
        } else {
            return $this->fs->isFile($this->getPathname()) && $this->fs->copy($this->getPathname(), $filePath);
        }
    }

    /**
     * Whitelist the file extension
     * @param string $extension
     * @return bool
     */
    protected function whitelisted(string $extension): bool
    {
        if (!preg_match('/(' . implode('|', $this->blacklistedExtensions) . ')$/i', $extension)) {
            return true;
        }

        return false;
    }
}
