<?php

namespace Quantum\Libraries\Storage\Adapters\GoogleDrive;

use Quantum\Libraries\Storage\FilesystemAdapterInterface;
use Exception;

class GoogleDriveFileSystemAdapter implements FilesystemAdapterInterface
{

    /**
     * @var GoogleDriveFileSystemAdapter|null
     */
    private static $instance = null;

    private $googleDriveApp;

    /**
     * GoogleDriveFileSystemAdapter constructor
     * @param GoogleDriveApp $googleDriveApp
     */
    private function __construct(GoogleDriveApp $googleDriveApp)
    {
        $this->googleDriveApp = $googleDriveApp;
    }

    /**
     * Get Instance
     * @param GoogleDriveApp $googleDriveApp
     * @return GoogleDriveFileSystemAdapter
     */
    public static function getInstance(GoogleDriveApp $googleDriveApp): GoogleDriveFileSystemAdapter
    {
        if (self::$instance === null) {
            self::$instance = new self($googleDriveApp);
        }

        return self::$instance;
    }

    public function makeDirectory(string $dirname, ?string $parentId = null): bool
    {
        try{
            $data = [
                'name' => $dirname,
                'mimeType' => GoogleDriveApp::FOLDER_MIMETYPE,
                'parents' => $parentId ? [$parentId] : ['root']
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
            if($this->isFile($filename)){
                return $this->googleDriveApp->rpcRequest(GoogleDriveApp::FILE_MEDIA_URL . '/' . $filename . '?uploadType=media', $content, 'PATCH', 'application/octet-stream');
            }else{
                $data = [
                    'name' => $filename,
                    'parents' => $parentId ? [$parentId] : ['root']
                ];

                $newFile = $this->googleDriveApp->rpcRequest(GoogleDriveApp::FILE_METADATA_URL, $data);

                return $this->put($newFile->id, $content);
            }
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
                'name' => $newName
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
                'parents' => [$dest]
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
            return !empty($meta['modifiedTime']) ? strtotime($meta['modifiedTime']) : false;
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
                'fields' => '*'
            ];
            $response = (array)$this->googleDriveApp->rpcRequest(GoogleDriveApp::FILE_METADATA_URL . '?' . http_build_query($params), [], 'GET');
            return $response["files"];
        } catch (Exception $e) {
            return false;
        }
    }
}