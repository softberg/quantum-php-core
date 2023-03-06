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
            $newZip = new Zip();

            $res = $newZip->addEmptyDir($this->filename, 'dirName');

            $this->assertTrue($res);
        }

        public function testAddFileToZip()
        {
            $newZip = new Zip();

            $res = $newZip->addFile($this->filename, 'composer.json', 'newFileName.josn');

            $this->assertTrue($res);

            $deleteFileUsingName = $newZip->deleteUsingName($this->filename, 'newFileName.josn');

            $this->assertTrue($deleteFileUsingName);
        }

        public function testAddMultipleFilesToZip()
        {
            $newZip = new Zip();

            $addMultipleFiles = $newZip->addMultipleFiles($this->filename, [
                'composerCopy.json' => 'composer.json',
                'phpunitCopy.xml' => 'phpunit.xml',
            ]);

            $this->assertTrue($addMultipleFiles);
        }

        public function testAddFromStringToZip()
        {
            $newZip = new Zip();

            $res = $newZip->addFromString($this->filename, 'newFileName.txt', 'Created new file for test');

            $this->assertTrue($res);
        }

        public function testExtractToFromZip()
        {
            $newZip = new Zip();

            $addFile = $newZip->addFromString($this->filename, 'fileForExtract.txt', 'Created new file for test');

            $this->assertTrue($addFile);

            $extract = $newZip->extractTo($this->filename, base_dir());

            $this->assertTrue($extract);

            $this->fs->remove(base_dir() . DS . 'fileForExtract.txt');
        }

        public function testZipCount()
        {
            $newZip = new Zip();

            $addMultipleFiles = $newZip->addMultipleFiles($this->filename, [
                'composerCopy.json' => 'composer.json',
                'phpunitCopy.xml' => 'phpunit.xml',
            ]);

            $this->assertTrue($addMultipleFiles);
            
            $zipCount = $newZip->count();

            $this->assertEquals(2, $zipCount);

        }

        public function testRenameUsingNameInZip()
        {
            $newZip = new Zip();

            $addFromString = $newZip->addFromString($this->filename, 'currentName.txt', 'Created new file for test');

            $this->assertTrue($addFromString);

            $renameUsingName = $newZip->renameUsingName($this->filename, 'currentName.txt', 'newName.txt');

            $this->assertTrue($renameUsingName);
        }

        public function testDeleteUsingNameFromZip()
        {
            $newZip = new Zip();

            $addFromString = $newZip->addFromString($this->filename, 'newName.txt', 'Created new file for test');

            $this->assertTrue($addFromString);

            $deleteUsingName = $newZip->deleteUsingName($this->filename, 'newName.txt');

            $this->assertTrue($deleteUsingName);
        }

        public function testDeleteMultipleFilesUsingNameFromZip()
        {
            $newZip = new Zip();

            $addMultipleFiles = $newZip->addMultipleFiles($this->filename, [
                'testDir/composerForDelete.json' => 'composer.json',
                'phpunitForDelete.xml' => 'phpunit.xml',
            ]);

            $this->assertTrue($addMultipleFiles);

            $deleteUsingName = $newZip->deleteMultipleFilesUsingName($this->filename, [
                'testDir/composerForDelete.json',
                'phpunitForDelete.xml',
            ]);

            $this->assertTrue($deleteUsingName);
        }
    }
}
