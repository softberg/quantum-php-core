<?php

namespace Quantum\Test\Unit {

    use PHPUnit\Framework\TestCase;
    use Quantum\Di\Di;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Libraries\Upload\File;
    use Quantum\Exceptions\FileUploadException;
    use Quantum\Http\Request;
    use Quantum\Loader\Loader;

    class FileDouble extends File
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

    class FileTest extends TestCase
    {
        public function setUp(): void
        {
            $this->request = new Request();

            $loader = new Loader(new FileSystem);

            $loader->loadDir(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers' . DS . 'functions');

            $loader->loadFile(dirname(__DIR__, 4) . DS . 'src' . DS . 'constants.php');

            Di::loadDefinitions();

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
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => dirname(__FILE__) . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new File($image);

            $this->assertInstanceOf(File::class, $file);
        }

        public function testGetSetName()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => dirname(__FILE__) . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new File($image);

            $this->assertEquals('foo', $file->getName());

            $file->setName('bar');

            $this->assertEquals('bar', $file->getName());
        }

        public function testGetExtension()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => dirname(__FILE__) . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new File($image);

            $this->assertEquals('jpg', $file->getExtension());
        }

        public function testGetNameWithExtension()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => dirname(__FILE__) . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new File($image);

            $this->assertEquals('foo.jpg', $file->getNameWithExtension());
        }

        public function testGetMimeType()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => dirname(__FILE__) . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new File($image);

            $this->assertSame('image/jpeg', $file->getMimeType());
        }

        public function testGetMd5()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => dirname(__FILE__) . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new File($image);

            $this->assertIsString($file->getMd5());
        }

        public function testGetDimensions()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => dirname(__FILE__) . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new File($image);

            $this->assertEquals(300, $file->getDimensions()['width']);

            $this->assertEquals(300, $file->getDimensions()['height']);
        }

        public function testUplaodWithoutFileSent()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => '/tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new File($image);

            $this->assertFalse($file->save(dirname(__FILE__)));
        }

        public function testUploadAndSave()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => dirname(__FILE__) . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new FileDouble($image);

            $this->assertInstanceOf(File::class, $file);

            $file->save(dirname(__FILE__));

            $this->assertTrue(file_exists(dirname(__FILE__) . DS . $file->getNameWithExtension()));
        }

        public function testSaveAndTryOWerwrite()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => dirname(__FILE__) . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new FileDouble($image);

            $file->save(dirname(__FILE__));

            $this->assertTrue(file_exists(dirname(__FILE__) . DS . $file->getNameWithExtension()));

            $this->expectException(FileUploadException::class);

            $this->expectExceptionMessage(FileUploadException::FILE_ALREADY_EXISTS);

            $file->save(dirname(__FILE__));
        }

        public function testSaveAndOWerwrite()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => dirname(__FILE__) . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new FileDouble($image);

            $file->save(dirname(__FILE__));

            $this->assertTrue(file_exists(dirname(__FILE__) . DS . $file->getNameWithExtension()));

            $this->assertTrue($file->save(dirname(__FILE__), true));
        }

        public function testModifyAndSave()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => dirname(__FILE__) . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new FileDouble($image);

            $file->modify('crop', [100, 100]);

            $file->save(dirname(__FILE__));

            $img = getimagesize(dirname(__FILE__) . DS . $file->getNameWithExtension());

            $this->assertEquals(100, $img[0]);

            $this->assertEquals(100, $img[1]);
        }

        public function testGetErrorCodeAndMessage()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => dirname(__FILE__),
                'type' => 'image/jpg',
                'error' => 1,
            ];

            $file = new File($image);

            $this->assertEquals(1, $file->getErrorCode());

            $this->assertEquals('The uploaded file exceeds the upload_max_filesize directive in php.ini', $file->getErrorMessage());
        }

    }

}