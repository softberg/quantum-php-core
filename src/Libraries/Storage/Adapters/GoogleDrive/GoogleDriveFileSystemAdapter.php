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
 * @since 3.0.0
 */

namespace Quantum\Libraries\Storage\Adapters\GoogleDrive;

use Quantum\Libraries\Storage\Contracts\FilesystemAdapterInterface;
use Exception;

/**
 * Class GoogleDriveFileSystemAdapter
 * @package Quantum\Libraries\Storage
 */
class GoogleDriveFileSystemAdapter implements FilesystemAdapterInterface
{
    /**
     * @var GoogleDriveApp
     */
    private $googleDriveApp;

    /**
     * @param GoogleDriveApp $googleDriveApp
     */
    public function __construct(GoogleDriveApp $googleDriveApp)
    {
        $this->googleDriveApp = $googleDriveApp;
    }

    /**
     * @inheritDoc
     */
    public function makeDirectory(string $dirname, ?string $parentId = null): bool
    {
        try {
            $data = [
                'name' => $dirname,
                'mimeType' => GoogleDriveApp::FOLDER_MIMETYPE,
                'parents' => $parentId ? [$parentId] : ['root'],
            ];

            $this->googleDriveApp->rpcRequest(GoogleDriveApp::FILE_METADATA_URL, $data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function removeDirectory(string $dirname): bool
    {
        return $this->remove($dirname);
    }

    /**
     * @inheritDoc
     */
    public function get(string $filename)
    {
        try {
            return (string)$this->googleDriveApp->getFileInfo($filename, true);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function put(string $filename, string $content, ?string $parentId = null)
    {
        try {
            if ($this->isFile($filename)) {
                $fileId = $filename;
            } else {
                $data = [
                    'name' => $filename,
                    'parents' => $parentId ? [$parentId] : ['root'],
                ];

                $newFile = $this->googleDriveApp->rpcRequest(
                    GoogleDriveApp::FILE_METADATA_URL,
                    $data
                );

                if (!isset($newFile->id)) {
                    throw new Exception('Google Drive file creation failed');
                }

                $fileId = $newFile->id;
            }

            return $this->googleDriveApp->rpcRequest(
                GoogleDriveApp::FILE_MEDIA_URL . '/' . $fileId . '?uploadType=media',
                $content,
                'PUT',
                'application/octet-stream'
            );

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function append(string $filename, string $content)
    {
        $fileContent = $this->get($filename);

        return $this->put($filename, $fileContent . $content);
    }

    /**
     * @inheritDoc
     */
    public function rename(string $oldName, string $newName): bool
    {
        try {
            $data = [
                'name' => $newName,
            ];

            $this->googleDriveApp->rpcRequest(GoogleDriveApp::FILE_METADATA_URL . '/' . $oldName, $data, 'PATCH');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function copy(string $source, string $dest = 'root'): bool
    {
        try {
            $data = [
                'parents' => [$dest],
            ];

            $this->googleDriveApp->rpcRequest(GoogleDriveApp::FILE_METADATA_URL . '/' . $source . '/copy', $data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function exists(string $filename): bool
    {
        return $this->isFile($filename);
    }

    /**
     * @inheritDoc
     */
    public function size(string $filename)
    {
        try {
            $meta = (array)$this->googleDriveApp->getFileInfo($filename);
            return $meta['size'];
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function lastModified(string $filename)
    {
        try {
            $meta = (array)$this->googleDriveApp->getFileInfo($filename);
            return empty($meta['modifiedTime']) ? false : strtotime($meta['modifiedTime']);
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * @inheritDoc
     */
    public function remove(string $filename): bool
    {
        try {
            $this->googleDriveApp->rpcRequest(GoogleDriveApp::FILE_METADATA_URL . '/' . $filename, [], 'DELETE');
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * @inheritDoc
     */
    public function isFile(string $filename): bool
    {
        try {
            $meta = (array)$this->googleDriveApp->getFileInfo($filename);

            return $meta['kind'] === GoogleDriveApp::DRIVE_FILE_KIND && $meta['mimeType'] != GoogleDriveApp::FOLDER_MIMETYPE;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function isDirectory(string $dirname): bool
    {
        try {
            $meta = (array)$this->googleDriveApp->getFileInfo($dirname);

            return $meta['kind'] === GoogleDriveApp::DRIVE_FILE_KIND && $meta['mimeType'] === GoogleDriveApp::FOLDER_MIMETYPE;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function listDirectory(string $dirname)
    {
        try {
            $params = [
                'q' => "'$dirname' in parents and trashed = false",
                'fields' => '*',
            ];
            $response = (array)$this->googleDriveApp->rpcRequest(GoogleDriveApp::FILE_METADATA_URL . '?' . http_build_query($params), [], 'GET');
            return $response['files'];
        } catch (Exception $e) {
            return false;
        }
    }
}
