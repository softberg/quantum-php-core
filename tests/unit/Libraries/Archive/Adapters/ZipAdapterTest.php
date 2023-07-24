<?php

namespace Quantum\Tests\Libraries\Archive\Adapters;

use Quantum\Libraries\Archive\Adapters\ZipAdapter;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Tests\AppTestCase;

class ZipAdapterTest extends AppTestCase
{

    private $fs;
    private $zipArchive;
    private $archiveName;

    public function setUp(): void
    {
        parent::setUp();
        $this->fs = new FileSystem();
        $this->archiveName = base_dir() . DS . 'test.zip';
        $this->zipArchive = new ZipAdapter($this->archiveName);
    }

    public function tearDown(): void
    {
        unset($this->zipArchive);

        if ($this->fs->exists($this->archiveName)) {
            $this->fs->remove($this->archiveName);
        }
    }

    public function testAddEmptyDirToZip()
    {
        $this->assertFalse($this->zipArchive->offsetExists('directoryOne'));

        $this->assertTrue($this->zipArchive->addEmptyDir('directoryOne'));

        $this->assertTrue($this->zipArchive->offsetExists('directoryOne'));
    }

    public function testAddFileToZip()
    {
        $this->assertFalse($this->zipArchive->offsetExists('app.conf'));

        $this->assertTrue(
            $this->zipArchive->addFile(
                base_dir() . DS . 'shared' . DS . 'config' . DS . 'config.php',
                'app.conf')
        );

        $this->assertTrue($this->zipArchive->offsetExists('app.conf'));
    }

    public function testAddFromStringToZip()
    {
        $this->assertTrue($this->zipArchive->addFromString('custom.text', 'Just a sample text'));

        $this->assertTrue($this->zipArchive->offsetExists('custom.text'));
    }

    public function testAddMultipleFilesToZip()
    {
        $this->assertTrue($this->zipArchive->addMultipleFiles([
            'app.conf' => base_dir() . DS . 'shared' . DS . 'config' . DS . 'config.php',
            'session.conf' => base_dir() . DS . 'shared' . DS . 'config' . DS . 'session.php'
        ]));

        $this->assertTrue($this->zipArchive->offsetExists('app.conf'));

        $this->assertTrue($this->zipArchive->offsetExists('session.conf'));
    }

    public function testZipCount()
    {
        $this->assertTrue($this->zipArchive->addMultipleFiles([
            'app.conf' => base_dir() . DS . 'shared' . DS . 'config' . DS . 'config.php',
            'session.conf' => base_dir() . DS . 'shared' . DS . 'config' . DS . 'session.php'
        ]));

        $this->assertEquals(2, $this->zipArchive->count());
    }

    public function testExtractToFromZip()
    {
        $content = 'Just a sample text';

        $this->zipArchive->addFromString('sample.txt', $content);

        $this->assertTrue($this->zipArchive->extractTo(base_dir()));

        $this->assertFileExists(base_dir() . DS . 'sample.txt');

        $this->assertEquals($content, $this->fs->get(base_dir() . DS . 'sample.txt'));

        $this->fs->remove(base_dir() . DS . 'sample.txt');
    }

    public function testDeleteFileFromZip()
    {
        $this->zipArchive->addMultipleFiles([
            'app.conf' => base_dir() . DS . 'shared' . DS . 'config' . DS . 'config.php',
            'session.conf' => base_dir() . DS . 'shared' . DS . 'config' . DS . 'session.php'
        ]);

        $this->assertEquals(2, $this->zipArchive->count());

        $this->zipArchive->addFromString('sample.txt', 'Just a sample text');

        $this->assertEquals(3, $this->zipArchive->count());

        $this->zipArchive->deleteFile('sample.txt');

        $this->assertFalse($this->zipArchive->offsetExists('sample.txt'));

        $this->assertEquals(2, $this->zipArchive->count());
    }

    public function testDeleteMultipleFilesFromZip()
    {
        $this->zipArchive->addMultipleFiles([
            'app.conf' => base_dir() . DS . 'shared' . DS . 'config' . DS . 'config.php',
            'directoryOne/session.conf' => base_dir() . DS . 'shared' . DS . 'config' . DS . 'session.php'
        ]);

        $this->assertEquals(2, $this->zipArchive->count());

        $this->assertTrue($this->zipArchive->offsetExists('app.conf'));
        $this->assertTrue($this->zipArchive->offsetExists('directoryOne/session.conf'));

        $this->assertTrue($this->zipArchive->deleteMultipleFiles([
            'directoryOne/session.conf',
            'app.conf',
        ]));

        $this->assertEquals(0, $this->zipArchive->count());

        $this->assertFalse($this->zipArchive->offsetExists('directoryOne/session.conf'));

        $this->assertFalse($this->zipArchive->offsetExists('app.conf'));
    }
}

