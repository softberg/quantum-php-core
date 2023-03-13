<?php

namespace Quantum\Tests\Libraries\Archive {

    use Quantum\Libraries\Archive\Adapters\PharAdapter;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Tests\AppTestCase;

    class PharTest extends AppTestCase
    {

        private $fs;
        private $newPhar;
        private $filename;

        public function setUp(): void
        {
            parent::setUp();
            $this->fs = new FileSystem();
            $this->newPhar = new PharAdapter(base_dir() . DS . 'test.phar');
            $this->filename = base_dir() . DS . 'test.phar';
        }

        public function tearDown(): void
        {
            if ($this->fs->exists($this->filename)) {
                $this->fs->remove($this->filename);
                $this->newPhar->removeArchive($this->filename);
            }
        }

        public function testAddEmptyDirToPhar()
        {
            $this->assertTrue($this->newPhar->addEmptyDir('dirName'));

            $this->assertTrue($this->newPhar->offsetExists('dirName'));

            $this->assertFileExists($this->filename, "filename doesn't exists");
        }

        public function testAddFileToPhar()
        {
            $this->assertTrue($this->newPhar->addFile('./composer.json', 'newFileName.josn'));

            $this->assertTrue($this->newPhar->offsetExists('newFileName.josn'));
        }

        public function testAddMultipleFilesToPhar()
        {
            $this->assertTrue($this->newPhar->addMultipleFiles([
                'composerCopy.json' => './composer.json',
                'phpunitCopy.xml' => './phpunit.xml',
            ]));

            $this->assertTrue($this->newPhar->offsetExists('composerCopy.json'));

            $this->assertTrue($this->newPhar->offsetExists('phpunitCopy.xml'));
        }

        public function testAddFromStringToPhar()
        {
            $this->assertTrue($this->newPhar->addFromString('newFileName.txt', 'Created new file for test'));

            $this->assertTrue($this->newPhar->offsetExists('newFileName.txt'));
        }

        public function testExtractToFromPhar()
        {
            $this->assertTrue($this->newPhar->addFromString('fileForExtract.txt', 'Created new file for test'));

            $this->assertTrue($this->newPhar->offsetExists('fileForExtract.txt'));

            $this->assertTrue($this->newPhar->extractTo(base_dir(), 'fileForExtract.txt'));

            $this->fs->remove(base_dir() . DS . 'fileForExtract.txt');
        }

        public function testPharCount()
        {
            $this->assertTrue($this->newPhar->addMultipleFiles([
                'composerCopy.json' => './composer.json',
                'phpunitCopy.xml' => './phpunit.xml',
            ]));

            $this->assertTrue($this->newPhar->offsetExists('composerCopy.json'));

            $this->assertTrue($this->newPhar->offsetExists('phpunitCopy.xml'));

            $this->assertEquals(2, $this->newPhar->count());
        }

        public function testDeleteUsingNameFromPhar()
        {
            $this->assertTrue($this->newPhar->addFromString('newFile.txt', 'Created new file for test'));

            $this->assertTrue($this->newPhar->addFromString('newFile1.txt', 'Created new file for test'));

            $this->assertTrue($this->newPhar->deleteUsingName('newFile.txt'));

            $this->assertFalse($this->newPhar->offsetExists('newFile.txt'));

            $this->assertTrue($this->newPhar->offsetExists('newFile1.txt'));
        }

        public function testDeleteMultipleFilesUsingNameFromPhar()
        {
            $this->assertTrue($this->newPhar->addFromString('newFile.txt', 'Created new file for test'));

            $this->assertTrue($this->newPhar->addMultipleFiles([
                'testDir/composerForDelete.json' => './composer.json',
                'phpunitForDelete.xml' => './phpunit.xml',
            ]));

            $this->assertTrue($this->newPhar->deleteMultipleFilesUsingName([
                'testDir/composerForDelete.json',
                'phpunitForDelete.xml',
            ]));

            $this->assertTrue($this->newPhar->offsetExists('newFile.txt'));

            $this->assertFalse($this->newPhar->offsetExists('testDir/composerForDelete.json'));

            $this->assertFalse($this->newPhar->offsetExists('phpunitForDelete.xml'));
        }
    }
}
