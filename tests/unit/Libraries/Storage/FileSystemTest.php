<?php

namespace Aws\FilesystemAdapter {

    use Quantum\Libraries\Storage\FilesystemAdapterInterface;
    use Quantum\Libraries\Storage\LocalFileSystemAdapter;

    class AwsS3V3Adapter extends LocalFileSystemAdapter implements FilesystemAdapterInterface
    {
        // Class body
    }

}

namespace Quantum\Tests\Libraries\Storage {

    use Quantum\Libraries\Storage\FilesystemAdapterInterface;
    use Quantum\Libraries\Storage\LocalFileSystemAdapter;
    use Quantum\Libraries\Storage\FileSystem;
    use Aws\FilesystemAdapter\AwsS3V3Adapter;
    use Quantum\Tests\AppTestCase;

    class FileSystemTest extends AppTestCase
    {

        private $fs;
        private $filename;
        private $content = 'Hello world';

        public function setUp(): void
        {
            parent::setUp();

            $this->fs = new FileSystem();

            $this->filename = base_dir() . DS . 'test.txt';
        }

        public function tearDown(): void
        {
            if ($this->fs->exists($this->filename)) {
                $this->fs->remove($this->filename);
            }
        }

        public function testFileSystemAdapter()
        {
            $this->assertInstanceOf(FilesystemAdapterInterface::class, $this->fs->getAdapter());

            $this->assertInstanceOf(LocalFileSystemAdapter::class, $this->fs->getAdapter());

            $awsFs = new FileSystem(new AwsS3V3Adapter);

            $this->assertInstanceOf(AwsS3V3Adapter::class, $awsFs->getAdapter());
        }

        public function testMakeRemoveDirectory()
        {
            $dirName = base_dir() . DS . 'testing';

            $this->assertDirectoryDoesNotExist($dirName);

            $this->fs->makeDirectory($dirName);

            $this->assertDirectoryExists($dirName);

            $this->fs->removeDirectory($dirName);

            $this->assertDirectoryDoesNotExist($dirName);
        }

        public function testFileGetPut()
        {
            $this->fs->put($this->filename, $this->content);

            $this->assertFileExists($this->filename);

            $this->assertEquals($this->content, $this->fs->get($this->filename));
        }

        public function testFileGetAppend()
        {
            $this->fs->put($this->filename, $this->content);

            $this->assertFileExists($this->filename);

            $moreContent = 'The sun is shining';

            $this->fs->append($this->filename, $moreContent);

            $this->assertEquals($this->content . $moreContent, $this->fs->get($this->filename));
        }

        public function testFileRename()
        {
            $this->fs->put($this->filename, '');

            $newFilename = base_dir() . DS . 'new_name.txt';

            $this->assertFileDoesNotExist($newFilename);

            $this->fs->rename($this->filename, $newFilename);

            $this->assertFileExists($newFilename);

            $this->fs->remove($newFilename);
        }

        public function testFileCopy()
        {
            $dirName = base_dir() . DS . 'testing';

            $this->fs->put($this->filename, '');

            $this->assertFileDoesNotExist($dirName . DS . $this->filename);

            $this->fs->makeDirectory($dirName);

            $this->fs->copy($this->filename, $dirName . DS . 'test.txt');

            $this->assertFileExists($dirName . DS . 'test.txt');

            $this->fs->remove($dirName . DS . 'test.txt');

            $this->fs->removeDirectory($dirName);
        }

        public function testFileExists()
        {
            $newFilename = base_dir() . DS . 'new_name.txt';

            $this->assertFalse($this->fs->exists($newFilename));

            $this->fs->put($newFilename, '');

            $this->assertTrue($this->fs->exists($newFilename));

            $this->fs->remove($newFilename);
        }

        public function testFileSize()
        {
            $this->fs->put($this->filename, 'some bytes');

            $this->assertEquals(10, $this->fs->size($this->filename));
        }

        public function testFileLastModified()
        {
            $this->fs->put($this->filename, '');

            $this->assertIsInt($this->fs->lastModified($this->filename));
        }

        public function testFileRemove()
        {
            $this->fs->put($this->filename, '');

            $this->assertFileExists($this->filename);

            $this->fs->remove($this->filename);

            $this->assertFileDoesNotExist($this->filename);
        }

        public function testIsFile()
        {
            $this->assertTrue($this->fs->isFile(__FILE__));

            $this->assertFalse($this->fs->isFile(__DIR__));
        }

        public function testIsDirectory()
        {
            $this->assertTrue($this->fs->isDirectory(__DIR__));

            $this->assertFalse($this->fs->isDirectory(__FILE__));
        }

        public function testFileIsReadableWritable()
        {
            $this->fs->put($this->filename, '');

            $this->assertTrue($this->fs->isReadable($this->filename));

            $this->assertTrue($this->fs->isWritable($this->filename));

            $this->fs->remove($this->filename);

            $this->assertFalse($this->fs->isReadable($this->filename));

            $this->assertFalse($this->fs->isWritable($this->filename));
        }

        public function testFileGetLines()
        {
            $lineOne = 'It\'s was started then ' . PHP_EOL;
            $lineTwo = 'something happens' . PHP_EOL;
            $lineThree = 'between lines' . PHP_EOL;
            $lineFour = 'and it\'s gone' . PHP_EOL;

            $this->fs->put($this->filename, $lineOne . $lineTwo . $lineThree . $lineFour);

            $lines = $this->fs->getLines($this->filename, 1, 2);

            $this->assertIsArray($lines);

            $this->assertEquals($lineTwo . $lineThree, implode('', $lines));
        }

        public function testFileNameAndExtension()
        {
            $this->assertEquals('test', $this->fs->fileName($this->filename));

            $this->assertEquals('txt', $this->fs->extension($this->filename));
        }

        public function testGlob()
        {
            $this->fs->put($this->filename, '');

            $this->assertIsArray($this->fs->glob(base_dir() . DS . '*.txt'));

            $this->assertEquals($this->filename, $this->fs->glob(base_dir() . DS . '*.txt')[0]);
        }

    }

}