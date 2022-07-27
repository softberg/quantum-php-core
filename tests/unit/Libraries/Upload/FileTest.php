<?php

namespace Quantum\Tests\Libraries\Upload {

    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\FileSystemException;
    use Quantum\Libraries\Upload\File;
    use Quantum\Di\Di;
    use Quantum\App;

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
            App::loadCoreFunctions(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers');

            App::setBaseDir(dirname(__DIR__, 2) . DS . '_root');

            Di::loadDefinitions();

            $this->file = [
                'image' => [
                    'size' => 500,
                    'name' => 'foo.jpg',
                    'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
                    'type' => 'image/jpg',
                    'error' => 0,
                ],
            ];
        }

        public function tearDown(): void
        {
            if (file_exists(base_dir() . DS . 'foo.jpg')) {
                unlink(base_dir() . DS . 'foo.jpg');
            }
        }

        public function testFileConstructor()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
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
                'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
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
                'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
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
                'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
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
                'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
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
                'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
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
                'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new File($image);

            $this->assertEquals(300, $file->getDimensions()['width']);

            $this->assertEquals(300, $file->getDimensions()['height']);
        }

        public function testUploadWithoutFileSent()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => '/tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new File($image);

            $this->assertFalse($file->save(base_dir()));
        }

        public function testUploadAndSave()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new FileDouble($image);

            $this->assertInstanceOf(File::class, $file);

            $file->save(base_dir());

            $this->assertTrue(file_exists(base_dir() . DS . $file->getNameWithExtension()));
        }

        public function testSaveAndTryOverwrite()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new FileDouble($image);

            $file->save(base_dir());

            $this->assertTrue(file_exists(base_dir() . DS . $file->getNameWithExtension()));

            $this->expectException(FileSystemException::class);

            $this->expectExceptionMessage(t('file_already_exists'));

            $file->save(base_dir());
        }

        public function testSaveAndOWerwrite()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new FileDouble($image);

            $file->save(base_dir());

            $this->assertTrue(file_exists(base_dir() . DS . $file->getNameWithExtension()));

            $this->assertTrue($file->save(base_dir(), true));
        }

        public function testModifyAndSave()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => base_dir() . DS . 'php8fe1.tmp',
                'type' => 'image/jpg',
                'error' => 0,
            ];

            $file = new FileDouble($image);

            $file->modify('crop', [100, 100]);

            $file->save(base_dir());

            $img = getimagesize(base_dir() . DS . $file->getNameWithExtension());

            $this->assertEquals(100, $img[0]);

            $this->assertEquals(100, $img[1]);
        }

        public function testGetErrorCodeAndMessage()
        {
            $image = [
                'size' => 500,
                'name' => 'foo.jpg',
                'tmp_name' => base_dir(),
                'type' => 'image/jpg',
                'error' => 1,
            ];

            $file = new File($image);

            $this->assertEquals(1, $file->getErrorCode());

            $this->assertEquals('The uploaded file exceeds the upload_max_filesize directive in php.ini', $file->getErrorMessage());
        }

    }

}