<?php

namespace Quantum\Tests\Libraries\Storage\Adapters\Local;

use Quantum\Libraries\Storage\Adapters\Local\LocalFileSystemAdapter;
use Quantum\Tests\AppTestCase;

class LocalFileSystemAdapterTest extends AppTestCase
{

    private $fs;
    private $dirname;
    private $filename;
    private $content = 'Hello world';

    public function setUp(): void
    {
        parent::setUp();

        $this->fs = LocalFileSystemAdapter::getInstance();

        $this->dirname = base_dir() . DS . 'common';

        $this->filename = base_dir() . DS . 'test.txt';
    }

    public function tearDown(): void
    {
        if ($this->fs->exists($this->filename)) {
            $this->fs->remove($this->filename);
        }
    }

    public function testLocalMakeRemoveDirectory()
    {
        $this->assertDirectoryDoesNotExist($this->dirname);

        $this->fs->makeDirectory($this->dirname);

        $this->assertDirectoryExists($this->dirname);

        $this->fs->removeDirectory($this->dirname);

        $this->assertDirectoryDoesNotExist($this->dirname);
    }

    public function testLocalFileGetPut()
    {
        $this->fs->put($this->filename, $this->content);

        $this->assertFileExists($this->filename);

        $this->assertEquals($this->content, $this->fs->get($this->filename));
    }

    public function testLocalFileAppend()
    {
        $this->fs->put($this->filename, $this->content);

        $this->assertFileExists($this->filename);

        $moreContent = 'The sun is shining';

        $this->fs->append($this->filename, $moreContent);

        $this->assertEquals($this->content . $moreContent, $this->fs->get($this->filename));
    }

    public function tesLocalFileRename()
    {
        $this->fs->put($this->filename, $this->content);

        $newFilename = base_dir() . DS . 'new_name.txt';

        $this->assertFileDoesNotExist($newFilename);

        $this->fs->rename($this->filename, $newFilename);

        $this->assertFileExists($newFilename);

        $this->fs->remove($newFilename);
    }

    public function testLocalFileCopy()
    {
        $this->fs->makeDirectory($this->dirname);

        $this->fs->put($this->filename, $this->content);

        $this->assertFileDoesNotExist($this->dirname . DS . $this->filename);

        $this->fs->copy($this->filename, $this->dirname . DS . 'test.txt');

        $this->assertFileExists($this->dirname . DS . 'test.txt');

        $this->fs->remove($this->dirname . DS . 'test.txt');

        $this->fs->removeDirectory($this->dirname);
    }

    public function testLocalFileExists()
    {
        $newFilename = base_dir() . DS . 'new_name.txt';

        $this->assertFalse($this->fs->exists($newFilename));

        $this->fs->put($newFilename, $this->content);

        $this->assertTrue($this->fs->exists($newFilename));

        $this->fs->remove($newFilename);
    }

    public function testLocalFileSize()
    {
        $this->fs->put($this->filename, 'some bytes');

        $this->assertEquals(10, $this->fs->size($this->filename));
    }

    public function testLocalFileLastModified()
    {
        $this->fs->put($this->filename, $this->content);

        $this->assertIsInt($this->fs->lastModified($this->filename));
    }

    public function testLocalFileRemove()
    {
        $this->fs->put($this->filename, $this->content);

        $this->assertFileExists($this->filename);

        $this->fs->remove($this->filename);

        $this->assertFileDoesNotExist($this->filename);
    }

    public function testLocalIsFile()
    {
        $this->assertTrue($this->fs->isFile(__FILE__));

        $this->assertFalse($this->fs->isFile(__DIR__));
    }

    public function testLocalIsDirectory()
    {
        $this->assertTrue($this->fs->isDirectory(__DIR__));

        $this->assertFalse($this->fs->isDirectory(__FILE__));
    }

    public function testLocalListDirectory()
    {
        $entries = $this->fs->listDirectory(base_dir());

        $this->assertIsArray($entries);

        $this->assertIsString(current($entries));

        $this->assertFalse($this->fs->listDirectory('nowhere'));
    }

    public function testLocalGlob()
    {
        $entries = $this->fs->glob(base_dir() . DS . '*.log');

        $this->assertIsArray($entries);

        $this->assertIsString(current($entries));
    }

    public function testLocalFileIsReadableWritable()
    {
        $this->fs->put($this->filename, '');

        $this->assertTrue($this->fs->isReadable($this->filename));

        $this->assertTrue($this->fs->isWritable($this->filename));

        $this->fs->remove($this->filename);

        $this->assertFalse($this->fs->isReadable($this->filename));

        $this->assertFalse($this->fs->isWritable($this->filename));
    }

    public function testLocalFileGetLines()
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

    public function testLocalFileNameAndExtension()
    {
        $this->assertEquals('test', $this->fs->fileName($this->filename));

        $this->assertEquals('txt', $this->fs->extension($this->filename));
    }


}

