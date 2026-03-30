<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Storage;

use Quantum\Storage\Contracts\LocalFilesystemAdapterInterface;
use Quantum\Storage\Contracts\FilesystemAdapterInterface;
use Quantum\Storage\Exceptions\FileSystemException;
use Quantum\Storage\Exceptions\FileUploadException;
use Quantum\Environment\Exceptions\EnvException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Lang\Exceptions\LangException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Gumlet\ImageResizeException;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
use ReflectionException;
use Gumlet\ImageResize;
use RuntimeException;
use Quantum\Di\Di;
use SplFileInfo;
use finfo;

/**
 * Class File
 * @package Quantum\Storage
 */
class UploadedFile extends SplFileInfo
{
    /**
     * Local File System
     */
    protected LocalFilesystemAdapterInterface $localFileSystem;

    /**
     * Remove File System
     */
    protected ?FilesystemAdapterInterface $remoteFileSystem = null;

    /**
     * Original file name provided by client
     */
    protected ?string $originalName = null;

    /**
     * File name (without extension)
     */
    protected ?string $name = null;

    /**
     * File extension
     */
    protected ?string $extension = null;

    /**
     * File mime type
     */
    protected ?string $mimetype = null;

    /**
     * ImageResize function name
     */
    protected ?string $imageModifierFuncName = null;

    /**
     * ImageResize function arguments
     */
    /**
     * @var array<string, mixed>
     */
    protected array $params = [];

    /**
     * Upload error code messages
     * @var array<int, string>
     */
    protected array $errorMessages = [
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload',
    ];

    /**
     * Allowed mime types => allowed extensions map
     * @var array<string, list<string>>
     */
    protected array $allowedMimeTypes = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'application/pdf' => ['pdf'],
    ];

    /**
     * Upload error code
     */
    protected int $errorCode;

    /**
     * @param array<string, mixed> $meta
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public function __construct(array $meta)
    {
        $adapter = fs()->getAdapter();

        if (!$adapter instanceof LocalFilesystemAdapterInterface) {
            throw FileSystemException::notInstanceOf(
                get_class($adapter),
                LocalFilesystemAdapterInterface::class
            );
        }

        $this->localFileSystem = $adapter;

        $this->originalName = $meta['name'];
        $this->errorCode = $meta['error'];

        $this->loadAllowedMimeTypesFromConfig();

        parent::__construct($meta['tmp_name']);
    }

    /**
     * Sets the allowed mime types => extensions map
     * @param array<string, string> $allowedMimeTypes
     */
    public function setAllowedMimeTypes(array $allowedMimeTypes, bool $merge = true): UploadedFile
    {
        $this->setAllowedMimeTypesMap($allowedMimeTypes, $merge);
        return $this;
    }

    /**
     * Get name
     */
    public function getName(): string
    {
        if (!$this->name) {
            $this->name = $this->localFileSystem->fileName($this->originalName ?? '');
        }

        return $this->name;
    }

    /**
     * Set name (without extension)
     */
    public function setName(string $name): UploadedFile
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets the remote file system adapter
     */
    public function setRemoteFileSystem(FilesystemAdapterInterface $remoteFileSystem): UploadedFile
    {
        $this->remoteFileSystem = $remoteFileSystem;
        return $this;
    }

    /**
     * Gets the remote file system adapter
     */
    public function getRemoteFileSystem(): ?FilesystemAdapterInterface
    {
        return $this->remoteFileSystem;
    }

    /**
     * Get file extension (without leading dot)
     */
    public function getExtension(): string
    {
        if (!$this->extension) {
            $this->extension = strtolower($this->localFileSystem->extension($this->originalName ?? ''));
        }

        return $this->extension;
    }

    /**
     * Get file name with extension
     */
    public function getNameWithExtension(): string
    {
        return $this->getName() . '.' . $this->getExtension();
    }

    /**
     * Get mime type
     */
    public function getMimeType(): string
    {
        if (!$this->mimetype) {
            $fileInfo = new finfo(FILEINFO_MIME);
            $mimetype = $fileInfo->file($this->getPathname());
            $mimetypeParts = is_string($mimetype) ? preg_split('/\s*[;,]\s*/', $mimetype) : false;
            $this->mimetype = strtolower(is_array($mimetypeParts) ? $mimetypeParts[0] : '');
            unset($fileInfo);
        }

        return $this->mimetype;
    }

    /**
     * Get md5
     */
    public function getMd5(): string
    {
        return md5_file($this->getPathname()) ?: '';
    }

    /**
     * Get image dimensions
     * @return array{width: int<0, max>, height: int<0, max>}
     * @throws FileUploadException
     */
    public function getDimensions(): array
    {
        if (!$this->isImage($this->getPathname())) {
            throw FileUploadException::fileTypeNotAllowed($this->getExtension());
        }

        $size = getimagesize($this->getPathname());

        if ($size === false) {
            throw FileUploadException::fileTypeNotAllowed($this->getExtension());
        }

        return [
            'width' => $size[0],
            'height' => $size[1],
        ];
    }

    /**
     * Save the uploaded file
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

        if (!$this->allowed($this->getExtension(), $this->getMimeType())) {
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
                throw FileSystemException::fileAlreadyExists($filePath);
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
     * @param array<mixed> $params
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
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * Gets the error message from code
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessages[$this->errorCode];
    }

    /**
     * Tells whether the file was uploaded
     */
    public function isUploaded(): bool
    {
        return is_uploaded_file($this->getPathname());
    }

    /**
     * Checks if the given file is image
     * @param string $filePath
     */
    public function isImage($filePath): bool
    {
        return (bool) getimagesize($filePath);
    }

    /**
     * Moves an uploaded file to a new location
     */
    protected function moveUploadedFile(string $filePath): bool
    {
        if ($this->remoteFileSystem) {
            return (bool) $this->remoteFileSystem->put($filePath, $this->localFileSystem->get($this->getPathname()));
        } elseif ($this->isUploaded()) {
            return move_uploaded_file($this->getPathname(), $filePath);
        } else {
            return $this->localFileSystem->copy($this->getPathname(), $filePath);
        }
    }

    /**
     * Validates upload against allowed mime types => extensions map
     */
    protected function allowed(string $extension, string $mimeType): bool
    {
        $extension = strtolower($extension);
        $mimeType = strtolower($mimeType);

        return isset($this->allowedMimeTypes[$mimeType]) &&
            in_array($extension, (array) $this->allowedMimeTypes[$mimeType], true);
    }

    /**
     * Loads allowed mime types from config (shared/config/uploads.php) if present.
     * @throws ConfigException
     * @throws DiException
     * @throws FileUploadException
     * @throws ReflectionException
     */
    protected function loadAllowedMimeTypesFromConfig(): void
    {
        if (!config()->has('uploads')) {
            $loader = Di::get(Loader::class);
            $setup = new Setup('config', 'uploads');
            $loader->setup($setup);

            if (!$loader->fileExists()) {
                return;
            }

            config()->import($setup);
        }

        $allowedMimeTypesMap = config()->get('uploads.allowed_mime_types') ?? [];

        if (!is_array($allowedMimeTypesMap)) {
            throw FileUploadException::incorrectMimeTypesConfig('uploads');
        }

        if ($allowedMimeTypesMap !== []) {
            $this->setAllowedMimeTypesMap($allowedMimeTypesMap);
        }
    }

    /**
     * Sets the allowed mime types => extensions map
     * @param array<string, string> $allowedMimeTypes
     */
    protected function setAllowedMimeTypesMap(array $allowedMimeTypes, bool $merge = true): void
    {
        $this->allowedMimeTypes = $merge ? array_merge_recursive($this->allowedMimeTypes, $allowedMimeTypes) : $allowedMimeTypes;
    }

    /**
     * Applies modifications on image
     * @param string $filePath
     * @return void
     * @throws ImageResizeException
     */
    protected function applyModifications(string $filePath)
    {
        $image = new ImageResize($filePath);
        $callable = [$image, $this->imageModifierFuncName ?? ''];

        if (!is_callable($callable)) {
            throw new RuntimeException('Invalid image modifier: ' . ($this->imageModifierFuncName ?? 'null'));
        }

        call_user_func_array($callable, array_values($this->params));

        $image->save($filePath);
    }
}
