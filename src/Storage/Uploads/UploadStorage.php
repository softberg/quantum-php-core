<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 3.0.0
 */

namespace Quantum\Storage\Uploads;

use Quantum\Storage\Contracts\LocalFilesystemAdapterInterface;
use Quantum\Storage\Contracts\FilesystemAdapterInterface;
use Quantum\Storage\UploadedFile;

class UploadStorage
{
    private LocalFilesystemAdapterInterface $localFileSystem;

    public function __construct(LocalFilesystemAdapterInterface $localFileSystem)
    {
        $this->localFileSystem = $localFileSystem;
    }

    public function store(UploadedFile $file, string $targetPath, ?FilesystemAdapterInterface $remoteFileSystem = null): bool
    {
        if ($remoteFileSystem) {
            return (bool) $remoteFileSystem->put($targetPath, $this->localFileSystem->get($file->getPathname()));
        }

        if ($file->isUploaded()) {
            return move_uploaded_file($file->getPathname(), $targetPath);
        }

        return $this->localFileSystem->copy($file->getPathname(), $targetPath);
    }
}
