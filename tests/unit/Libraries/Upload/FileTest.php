<?php

namespace Quantum\Test\Unit {

    use Mockery;
    use PHPUnit\Framework\TestCase;
    use Quantum\Libraries\Upload\File;
    use Quantum\Exceptions\FileUploadException;
    use Quantum\Exceptions\ExceptionMessages;
    use Quantum\Http\Request;

    class FileDouble extends File
    {

        public function isUploaded()
        {
            return file_exists($this->getPathname());
        }

        protected function moveUploadedFile($filePath)
        {
            return copy($this->getPathname(), $filePath);
        }

    }

    class FileTest extends TestCase
    {

        private $request;
        private $file;

        public function setUp(): void
        {
            $this->request = new Request();

            $this->file = [
                'image' => [
                    'size' => 500,
                    'name' => 'foo.jpg',
                    'tmp_name' => dirname(__FILE__) . DS . 'php8fe1.tmp',
                    'type' => 'image/jpg',
                    'error' => 0,
                ],
            ];
        }

        public function tearDown(): void
        {
            if (file_exists(dirname(__FILE__) . DS . 'foo.jpg')) {
                unlink(dirname(__FILE__) . DS . 'foo.jpg');
            }
        }

        public function testFileConstructor()
        {
            $this->request->create('POST', '/upload', null, $this->file);

            $image = $this->request->getFile('image');

            $file = new File($image);

            $this->assertInstanceOf('Quantum\Libraries\Upload\File', $file);
        }

        public function testGetSetName()
        {
            $this->request->create('POST', '/upload', null, $this->file);

            $image = $this->request->getFile('image');

            $file = new File($image);

            $this->assertEquals('foo', $file->getName());

            $file->setName('bar');

            $this->assertEquals('bar', $file->getName());
        }

        public function testGetExtension()
        {
            $this->request->create('POST', '/upload', null, $this->file);

            $image = $this->request->getFile('image');

            $file = new File($image);

            $this->assertEquals('jpg', $file->getExtension());
        }

        public function testGetNameWithExtension()
        {
            $this->request->create('POST', '/upload', null, $this->file);

            $image = $this->request->getFile('image');

            $file = new File($image);

            $this->assertEquals('foo.jpg', $file->getNameWithExtension());
        }

        public function testGetMimeType()
        {
            $this->request->create('POST', '/upload', null, $this->file);

            $image = $this->request->getFile('image');

            $file = new File($image);

            $this->assertSame('image/jpeg', $file->getMimeType());
        }

        public function testGetMd5()
        {
            $this->request->create('POST', '/upload', null, $this->file);

            $image = $this->request->getFile('image');

            $file = new File($image);

            $this->assertIsString($file->getMd5());
        }

        public function testGetDimensions()
        {
            $this->request->create('POST', '/upload', null, $this->file);

            $image = $this->request->getFile('image');

            $file = new File($image);

            $this->assertEquals(300, $file->getDimensions()['width']);

            $this->assertEquals(300, $file->getDimensions()['height']);
        }

        public function testUplaodWithError()
        {
            $file = [
                'image' => [
                    'size' => 500,
                    'name' => 'foo.jpg',
                    'tmp_name' => dirname(__FILE__) . '/php8fe1.tmp',
                    'type' => 'image/jpg',
                    'error' => 4,
                ],
            ];

            $this->request->create('POST', '/upload', null, $file);

            $image = $this->request->getFile('image');

            $this->expectException(FileUploadException::class);

            $this->expectExceptionMessage('No file was uploaded');

            $file = new File($image);

            $file->save(dirname(__FILE__));
        }

        public function testUplaodWithoutFileSent()
        {
            $file = [
                'image' => [
                    'size' => 500,
                    'name' => 'foo.jpg',
                    'tmp_name' => dirname(__FILE__),
                    'type' => 'image/jpg',
                    'error' => 0,
                ],
            ];

            $this->request->create('POST', '/upload', null, $file);

            $image = $this->request->getFile('image');

            $this->expectException(FileUploadException::class);

            $this->expectExceptionMessage(ExceptionMessages::FILE_NOT_UPLOADED);

            $file = new File($image);

            $file->save(dirname(__FILE__));
        }

        public function testUplaodAndSave()
        {
            $this->request->create('POST', '/upload', null, $this->file);

            $image = $this->request->getFile('image');

            $file = new FileDouble($image);

            $file->save(dirname(__FILE__));

            $this->assertTrue(file_exists(dirname(__FILE__) . DS . $file->getNameWithExtension()));
        }

        public function testSaveAndTryOWerwrite()
        {
            $this->request->create('POST', '/upload', null, $this->file);

            $image = $this->request->getFile('image');

            $file = new FileDouble($image);

            $file->save(dirname(__FILE__));

            $this->assertTrue(file_exists(dirname(__FILE__) . DS . $file->getNameWithExtension()));

            $this->expectException(FileUploadException::class);

            $this->expectExceptionMessage(ExceptionMessages::FILE_ALREADY_EXISTS);

            $file->save(dirname(__FILE__));
        }

        public function testSaveAndOWerwrite()
        {
            $this->request->create('POST', '/upload', null, $this->file);

            $image = $this->request->getFile('image');

            $file = new FileDouble($image);

            $file->save(dirname(__FILE__));

            $this->assertTrue(file_exists(dirname(__FILE__) . DS . $file->getNameWithExtension()));

            $this->assertTrue($file->save(dirname(__FILE__), true));
        }

        public function testModifyAndSave()
        {
            $this->request->create('POST', '/upload', null, $this->file);

            $image = $this->request->getFile('image');

            $file = new FileDouble($image);

            $file->modify('crop', [100, 100]);

            $file->save(dirname(__FILE__));

            $img = getimagesize(dirname(__FILE__) . DS . $file->getNameWithExtension());

            $this->assertEquals(100, $img[0]);

            $this->assertEquals(100, $img[1]);
        }

        public function testGetErrorCodeAndMessage()
        {
            $file = [
                'image' => [
                    'size' => 500,
                    'name' => 'foo.jpg',
                    'tmp_name' => dirname(__FILE__),
                    'type' => 'image/jpg',
                    'error' => 1,
                ],
            ];

            $this->request->create('POST', '/upload', null, $file);

            $image = $this->request->getFile('image');

            $file = new File($image);

            $this->assertEquals(1, $file->getErrorCode());

            $this->assertEquals('The uploaded file exceeds the upload_max_filesize directive in php.ini', $file->getErrorMessage());
        }

    }

}