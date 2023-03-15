<?php

namespace Quantum\Tests\Libraries\Archive {

    use Quantum\Libraries\Archive\Adapters\ZipAdapter;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Tests\AppTestCase;

    class ZipTest extends AppTestCase
    {

        private $fs;
        private $newZip;
        private $filename;

        public function setUp(): void
        {
            parent::setUp();
            $this->fs = new FileSystem();
            $this->filename = base_dir() . DS . 'test.zip';
            $this->newZip = new ZipAdapter($this->filename);
        }

        public function tearDown(): void
        {
            if ($this->fs->exists($this->filename)) {
                $this->fs->remove($this->filename);
            }
        }

        public function testAddEmptyDirToZip()
        {
            $this->assertFalse($this->newZip->offsetExists('dirName'));

            $this->assertTrue($this->newZip->addEmptyDir('dirName'));

            $this->assertTrue($this->newZip->offsetExists('dirName'));
        }

        public function testAddFileToZip()
        {
            $this->assertFalse($this->newZip->offsetExists('newFileName.josn'));

            $this->assertTrue($this->newZip->addFile('composer.json', 'newFileName.josn'));

            $this->assertTrue($this->newZip->offsetExists('newFileName.josn'));
        }

        public function testAddMultipleFilesToZip()
        {
            $this->assertTrue($this->newZip->addMultipleFiles([
                'composerCopy.json' => 'composer.json',
                'phpunitCopy.xml' => 'phpunit.xml',
            ]));

            $this->assertTrue($this->newZip->offsetExists('composerCopy.json'));

            $this->assertTrue($this->newZip->offsetExists('phpunitCopy.xml'));
        }

        public function testAddFromStringToZip()
        {
            $this->assertTrue($this->newZip->addFromString('newFileName.txt', 'Created new file for test'));

            $this->assertTrue($this->newZip->offsetExists('newFileName.txt'));
        }

        public function testExtractToFromZip()
        {
            $this->assertTrue($this->newZip->addFromString('fileForExtract.txt', 'Created new file for test'));

            $this->assertTrue($this->newZip->extractTo(base_dir()));

            $this->fs->remove(base_dir() . DS . 'fileForExtract.txt');
        }

        public function testZipCount()
        {
            $this->assertTrue($this->newZip->addMultipleFiles([
                'composerCopy.json' => 'composer.json',
                'phpunitCopy.xml' => 'phpunit.xml',
            ]));

            $this->assertTrue($this->newZip->offsetExists('composerCopy.json'));

            $this->assertTrue($this->newZip->offsetExists('phpunitCopy.xml'));

            $this->assertEquals(2, $this->newZip->count());
        }

        public function testDeleteUsingNameFromZip()
        {
            $this->assertTrue($this->newZip->addMultipleFiles([
                'composerCopy.json' => 'composer.json',
                'phpunitCopy.xml' => 'phpunit.xml',
            ]));

            $this->assertTrue($this->newZip->addFromString('newName.txt', 'Created new file for test'));

            $this->assertTrue($this->newZip->deleteUsingName('newName.txt'));

            $this->assertFalse($this->newZip->offsetExists('newName.txt'));

            $this->assertTrue($this->newZip->offsetExists('composerCopy.json'));

            $this->assertTrue($this->newZip->offsetExists('phpunitCopy.xml'));
        }

        public function testDeleteMultipleFilesUsingNameFromZip()
        {
            $this->assertTrue($this->newZip->addMultipleFiles([
                'testDir/composerForDelete.json' => 'composer.json',
                'composerCopy.json' => 'composer.json',
                'phpunitForDelete.xml' => 'phpunit.xml',
            ]));

            $this->assertTrue($this->newZip->deleteMultipleFilesUsingName([
                'testDir/composerForDelete.json',
                'phpunitForDelete.xml',
            ]));

            $this->assertFalse($this->newZip->offsetExists('testDir/composerForDelete.json'));

            $this->assertFalse($this->newZip->offsetExists('phpunitForDelete.xml'));

            $this->assertTrue($this->newZip->offsetExists('composerCopy.json'));
        }
    }
}
