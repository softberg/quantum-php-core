<?php

namespace Quantum\Tests\Libraries\Archive {

    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Libraries\Archive\Zip;
    use Quantum\Tests\AppTestCase;

    class ZipTest extends AppTestCase
    {

        private $fs;
        private $filename;

        public function setUp(): void
        {
            parent::setUp();
            $this->fs = new FileSystem();
            $this->filename = base_dir() . DS . 'test.zip';
        }

        public function tearDown(): void
        {
            if ($this->fs->exists($this->filename)) {
                $this->fs->remove($this->filename);
            }
        }

        public function testAddEmptyDirToZip()
        {
            $newZip = new Zip($this->filename);

            $this->assertFalse($newZip->offsetExists('dirName'));
            
            $this->assertTrue($newZip->addEmptyDir('dirName'));
            
            $this->assertTrue($newZip->offsetExists('dirName'));
        }
        
        public function testAddFileToZip()
        {
            $newZip = new Zip($this->filename);
            
            $this->assertFalse($newZip->offsetExists('newFileName.josn'));
            
            $this->assertTrue($newZip->addFile('composer.json', 'newFileName.josn'));
            
            $this->assertTrue($newZip->offsetExists('newFileName.josn'));
        }

        public function testAddMultipleFilesToZip()
        {
            $newZip = new Zip($this->filename);

            $this->assertTrue($newZip->addMultipleFiles([
                'composerCopy.json' => 'composer.json',
                'phpunitCopy.xml' => 'phpunit.xml',
            ]));

            $this->assertTrue($newZip->offsetExists('composerCopy.json'));

            $this->assertTrue($newZip->offsetExists('phpunitCopy.xml'));
        }

        public function testAddFromStringToZip()
        {
            $newZip = new Zip($this->filename);

            $this->assertTrue($newZip->addFromString('newFileName.txt', 'Created new file for test'));

            $this->assertTrue($newZip->offsetExists('newFileName.txt'));
        }

        public function testExtractToFromZip()
        {
            $newZip = new Zip($this->filename);

            $this->assertTrue($newZip->addFromString('fileForExtract.txt', 'Created new file for test'));

            $this->assertTrue($newZip->extractTo(base_dir()));

            $this->fs->remove(base_dir() . DS . 'fileForExtract.txt');
        }

        public function testZipCount()
        {
            $newZip = new Zip($this->filename);

            $this->assertTrue($newZip->addMultipleFiles([
                'composerCopy.json' => 'composer.json',
                'phpunitCopy.xml' => 'phpunit.xml',
            ]));
            
            $this->assertTrue($newZip->offsetExists('composerCopy.json'));

            $this->assertTrue($newZip->offsetExists('phpunitCopy.xml'));

            $this->assertEquals(2, $newZip->count());
        }

        public function testDeleteUsingNameFromZip()
        {
            $newZip = new Zip($this->filename);

            $this->assertTrue($newZip->addMultipleFiles([
                'composerCopy.json' => 'composer.json',
                'phpunitCopy.xml' => 'phpunit.xml',
            ]));

            $this->assertTrue($newZip->addFromString('newName.txt', 'Created new file for test'));

            $this->assertTrue($newZip->deleteUsingName('newName.txt'));

            $this->assertFalse($newZip->offsetExists('newName.txt'));

            $this->assertTrue($newZip->offsetExists('composerCopy.json'));

            $this->assertTrue($newZip->offsetExists('phpunitCopy.xml'));
        }

        public function testDeleteMultipleFilesUsingNameFromZip()
        {
            $newZip = new Zip($this->filename);

            $this->assertTrue($newZip->addMultipleFiles([
                'testDir/composerForDelete.json' => 'composer.json',
                'composerCopy.json' => 'composer.json',
                'phpunitForDelete.xml' => 'phpunit.xml',
            ]));

            $this->assertTrue($newZip->deleteMultipleFilesUsingName([
                'testDir/composerForDelete.json',
                'phpunitForDelete.xml',
            ]));

            $this->assertFalse($newZip->offsetExists('testDir/composerForDelete.json'));

            $this->assertFalse($newZip->offsetExists('phpunitForDelete.xml'));

            $this->assertTrue($newZip->offsetExists('composerCopy.json'));
        }
    }
}
