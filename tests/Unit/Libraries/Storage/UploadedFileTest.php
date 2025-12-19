<?php

namespace Quantum\Tests\Unit\Libraries\Storage;

use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxFileSystemAdapter;
use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Libraries\Storage\Exceptions\FileUploadException;
use Quantum\Libraries\Storage\Adapters\Dropbox\DropboxApp;
use Quantum\Libraries\Storage\UploadedFile;
use Quantum\Tests\Unit\AppTestCase;
use Mockery;

class UploadedDouble extends UploadedFile
{

    public function isUploaded(): bool
    {
        return file_exists($this->getPathname());
    }

    protected function moveUploadedFile(string $filePath): bool
    {
        return copy($this->getPathname(), $filePath);
    }

}

class UploadedFileTest extends AppTestCase
{

    private $fileMeta;

    public function setUp(): void
    {
        parent::setUp();

        $this->fileMeta = [
            'size' => 500,
            'name' => 'foo.jpg',
            'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
            'type' => 'image/jpg',
            'error' => 0,
        ];
    }

    public function tearDown(): void
    {
        if (file_exists(base_dir() . DS . 'foo.jpg')) {
            unlink(base_dir() . DS . 'foo.jpg');
        }

        if (file_exists(base_dir() . DS . 'foo.txt')) {
            unlink(base_dir() . DS . 'foo.txt');
        }
    }

    public function testUploadedFileConstructor()
    {
        $uploadedFile = new UploadedFile($this->fileMeta);

        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);
    }

    public function testUploadedFileGetSetName()
    {
        $uploadedFile = new UploadedFile($this->fileMeta);

        $this->assertEquals('foo', $uploadedFile->getName());

        $uploadedFile->setName('bar');

        $this->assertEquals('bar', $uploadedFile->getName());
    }

    public function testUploadedFileSetGetRemoteFileSystem()
    {
        $dropboxAppMock = Mockery::mock(DropboxApp::class);

        $uploadedFile = (new UploadedFile($this->fileMeta))
            ->setRemoteFileSystem(new DropboxFileSystemAdapter($dropboxAppMock));

        $this->assertInstanceOf(DropboxFileSystemAdapter::class, $uploadedFile->getRemoteFileSystem());

    }

    public function testUploadedFileGetExtension()
    {
        $uploadedFile = new UploadedFile($this->fileMeta);

        $this->assertEquals('jpg', $uploadedFile->getExtension());
    }

    public function testUploadedFileGetNameWithExtension()
    {
        $uploadedFile = new UploadedFile($this->fileMeta);

        $this->assertEquals('foo.jpg', $uploadedFile->getNameWithExtension());
    }

    public function testUploadedFileGetMimeType()
    {
        $uploadedFile = new UploadedFile($this->fileMeta);

        $this->assertSame('image/jpeg', $uploadedFile->getMimeType());
    }

    public function testUploadedFileGetMd5()
    {
        $uploadedFile = new UploadedFile($this->fileMeta);

        $this->assertIsString($uploadedFile->getMd5());
    }

    public function testUploadedFileGetDimensions()
    {
        $uploadedFile = new UploadedFile($this->fileMeta);

        $this->assertEquals(300, $uploadedFile->getDimensions()['width']);

        $this->assertEquals(300, $uploadedFile->getDimensions()['height']);
    }

    public function testUploadedFileUploadWithoutFileSent()
    {
        $fileMeta = [
            'size' => 500,
            'name' => 'foo.jpg',
            'tmp_name' => DS . 'tmp',
            'type' => 'image/jpg',
            'error' => 0,
        ];

        $uploadedFile = new UploadedFile($fileMeta);

        $this->expectException(FileUploadException::class);

        $this->expectExceptionMessage('The file `' . DS . 'tmp` not found');

        $this->assertFalse($uploadedFile->save(base_dir()));
    }

    public function testUploadedFileUploadAndSave()
    {
        $uploadedFile = new UploadedDouble($this->fileMeta);

        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);

        $uploadedFile->save(base_dir());

        $this->assertTrue(file_exists(base_dir() . DS . $uploadedFile->getNameWithExtension()));
    }

    public function testUploadedFileUploadNotAllowedExtensionForMimeType()
    {
        $fileMeta = $this->fileMeta;
        $fileMeta['name'] = 'foo.php';

        $uploadedFile = new UploadedDouble($fileMeta);

        $this->expectException(FileUploadException::class);
        $this->expectExceptionMessage('The file type `php` is not allowed.');

        $uploadedFile->save(base_dir());
    }

    public function testUploadedFileUploadNotAllowedMimeType()
    {
        $fileMeta = $this->fileMeta;
        $fileMeta['name'] = 'foo.jpg';
        $fileMeta['tmp_name'] = base_dir() . DS . 'journal.log';

        $uploadedFile = new UploadedDouble($fileMeta);

        $this->expectException(FileUploadException::class);
        $this->expectExceptionMessage('The file type `jpg` is not allowed.');

        $uploadedFile->save(base_dir());
    }

    public function testUploadedFileUploadAllowedAfterSetterOverride()
    {
        $fileMeta = $this->fileMeta;
        $fileMeta['name'] = 'foo.txt';
        $fileMeta['tmp_name'] = base_dir() . DS . 'journal.log';

        $uploadedFile = new UploadedDouble($fileMeta);

        $uploadedFile->setAllowedMimeTypes([
            $uploadedFile->getMimeType() => ['txt'],
        ]);

        $this->assertTrue($uploadedFile->save(base_dir()));
        $this->assertTrue(file_exists(base_dir() . DS . $uploadedFile->getNameWithExtension()));
    }

    public function testUploadedFileSaveAndTryOverwrite()
    {
        $uploadedFile = new UploadedDouble($this->fileMeta);

        $uploadedFile->save(base_dir());

        $filePath = base_dir() . DS . $uploadedFile->getNameWithExtension();

        $this->assertTrue(file_exists($filePath));

        $this->expectException(FileSystemException::class);

        $this->expectExceptionMessage('The file ' . $filePath . ' already exists');

        $uploadedFile->save(base_dir());
    }

    public function testUploadedFileSaveAndOverwrite()
    {
        $uploadedFile = new UploadedDouble($this->fileMeta);

        $uploadedFile->save(base_dir());

        $this->assertTrue(file_exists(base_dir() . DS . $uploadedFile->getNameWithExtension()));

        $this->assertTrue($uploadedFile->save(base_dir(), true));
    }

    public function testUploadedFileModifyAndSave()
    {
        $uploadedFile = new UploadedDouble($this->fileMeta);

        $uploadedFile->modify('crop', [100, 100]);

        $uploadedFile->save(base_dir());

        $img = getimagesize(base_dir() . DS . $uploadedFile->getNameWithExtension());

        $this->assertEquals(100, $img[0]);

        $this->assertEquals(100, $img[1]);
    }

    public function testUploadedFileGetErrorCodeAndMessage()
    {
        $fileMeta = [
            'size' => 500,
            'name' => 'foo . jpg',
            'tmp_name' => base_dir(),
            'type' => 'image / jpg',
            'error' => 1,
        ];

        $uploadedFile = new UploadedFile($fileMeta);

        $this->assertEquals(1, $uploadedFile->getErrorCode());

        $this->assertEquals('The uploaded file exceeds the upload_max_filesize directive in php.ini', $uploadedFile->getErrorMessage());
    }
}