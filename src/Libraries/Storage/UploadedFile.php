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

namespace Quantum\Libraries\Storage;

use Quantum\Libraries\Storage\Contracts\FilesystemAdapterInterface;
use Quantum\Libraries\Storage\Exceptions\FileUploadException;
use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Environment\Exceptions\EnvException;
use Quantum\Exceptions\BaseException;
use Gumlet\ImageResizeException;
use Gumlet\ImageResize;
use SplFileInfo;
use finfo;

/**
 * Class File
 * @package Quantum\Libraries\Storage
 */
class UploadedFile extends SplFileInfo
{

    /**
     * Local File System
     * @var FilesystemAdapterInterface
     */
    protected $localFileSystem;

    /**
     * Remove File System
     * @var FilesystemAdapterInterface
     */
    protected $remoteFileSystem = null;

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
    protected $imageModifierFuncName = null;

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
     * @param array $meta
     * @throws BaseException
     */
    public function __construct(array $meta)
    {
        $this->localFileSystem = FileSystemFactory::get();

        $this->originalName = $meta['name'];
        $this->errorCode = $meta['error'];

        parent::__construct($meta['tmp_name']);
    }

    /**
     * Get name
     * @return string
     */
    public function getName(): string
    {
        if (!$this->name) {
            $this->name = $this->localFileSystem->fileName($this->originalName);
        }

        return $this->name;
    }

    /**
     * Set name (without extension)
     * @param string $name
     * @return $this
     */
    public function setName(string $name): UploadedFile
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets the remote file system adapter
     * @param FilesystemAdapterInterface $remoteFileSystem
     * @return $this
     */
    public function setRemoteFileSystem(FilesystemAdapterInterface $remoteFileSystem): UploadedFile
    {
        $this->remoteFileSystem = $remoteFileSystem;
        return $this;
    }

    /**
     * Gets the remote file system adapter
     * @return FilesystemAdapterInterface|null
     */
    public function getRemoteFileSystem(): ?FilesystemAdapterInterface
    {
        return $this->remoteFileSystem;
    }

    /**
     * Get file extension (without leading dot)
     * @return string
     */
    public function getExtension(): string
    {
        if (!$this->extension) {
            $this->extension = strtolower($this->localFileSystem->extension($this->originalName));
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
            $fileInfo = new finfo(FILEINFO_MIME);
            $mimetype = $fileInfo->file($this->getPathname());
            $mimetypeParts = preg_split('/\s*[;,]\s*/', $mimetype);
            $this->mimetype = strtolower($mimetypeParts[0]);
            unset($fileInfo);
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
     * @throws FileUploadException
     */
    public function getDimensions(): array
    {
        if (!$this->isImage($this->getPathname())) {
            throw FileUploadException::fileTypeNotAllowed($this->getExtension());
        }

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
     * @throws BaseException
     * @throws FileSystemException
     * @throws FileUploadException
     * @throws ImageResizeException
     * @throws EnvException
     */
    public function save(string $dest, bool $overwrite = false): bool
    {
        if ($this->errorCode !== UPLOAD_ERR_OK) {
            throw new FileUploadException($this->getErrorMessage());
        }

        if (!$this->localFileSystem->isFile($this->getPathname())) {
            throw FileUploadException::fileNotFound($this->getPathname());
        }

        if (!$this->whitelisted($this->getExtension())) {
            throw FileUploadException::fileTypeNotAllowed($this->getExtension());
        }

        $filePath = $dest . DS . $this->getNameWithExtension();

        if (!$this->remoteFileSystem) {
            if (!$this->localFileSystem->isDirectory($dest)) {
                throw FileSystemException::directoryNotExists($dest);
            }

            if (!$this->localFileSystem->isWritable($dest)) {
                throw FileSystemException::directoryNotWritable($dest);
            }

            if ($overwrite === false && $this->localFileSystem->exists($filePath)) {
                throw FileSystemException::fileAlreadyExists();
            }
        }

        if (!$this->moveUploadedFile($filePath)) {
            return false;
        }

        if ($this->imageModifierFuncName) {
            $this->applyModifications($filePath);
        }

        return true;
    }

    /**
     * Sets modification function on image
     * @param string $funcName
     * @param array $params
     * @return $this
     * @throws BaseException
     * @throws FileUploadException
     * @throws LangException
     */
    public function modify(string $funcName, array $params): UploadedFile
    {
        if (!$this->isImage($this->getPathname())) {
            throw FileUploadException::fileTypeNotAllowed($this->getExtension());
        }

        if (!method_exists(ImageResize::class, $funcName)) {
            throw BaseException::methodNotSupported($funcName, ImageResize::class);
        }

        $this->imageModifierFuncName = $funcName;
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
     * Checks if the given file is image
     * @param $filePath
     * @return bool
     */
    public function isImage($filePath): bool
    {
        return !!getimagesize($filePath);
    }

    /**
     * Moves an uploaded file to a new location
     * @param string $filePath
     * @return bool
     */
    protected function moveUploadedFile(string $filePath): bool
    {
        if ($this->remoteFileSystem) {
            return (bool)$this->remoteFileSystem->put($filePath, $this->localFileSystem->get($this->getPathname()));
        } else {
            if ($this->isUploaded()) {
                return move_uploaded_file($this->getPathname(), $filePath);
            } else {
                return $this->localFileSystem->copy($this->getPathname(), $filePath);
            }
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

    /**
     * Applies modifications on image
     * @param $filePath
     * @throws ImageResizeException
     */
    protected function applyModifications($filePath)
    {
        $image = new ImageResize($filePath);
        call_user_func_array([$image, $this->imageModifierFuncName], $this->params);

        $image->save($filePath);
    }
}