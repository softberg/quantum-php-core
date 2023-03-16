<?php

namespace Quantum\Tests\Libraries\Archive {

    use Phar;
    use Quantum\Libraries\Archive\Adapters\PharAdapter;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Tests\AppTestCase;

    class PharTest extends AppTestCase
    {

        private $fs;
        private $newPhar;
        private $pharPath;

        public function setUp(): void
        {
            parent::setUp();
            $this->fs = new FileSystem();
            $this->pharPath = base_dir() . DS . 'test.phar';
            $this->newPhar = new PharAdapter($this->pharPath);
        }

        public function tearDown(): void
        {
            if ($this->fs->exists($this->pharPath)) {
                $this->fs->remove($this->pharPath);
                $this->newPhar->removeArchive($this->pharPath);
            }
        }

        public function testAddEmptyDirToPhar()
        {
            $this->assertTrue($this->newPhar->addEmptyDir('dirName'));

            $this->assertDirectoryExists("phar://{$this->pharPath}/dirName");

            $this->assertTrue($this->newPhar->offsetExists('dirName'));

            $this->assertFileExists($this->pharPath, "filename doesn't exists");
        }

        public function testAddFileToPhar()
        {
            $newFileName = 'newFileName.josn';

            $this->assertFalse($this->newPhar->offsetExists($newFileName));

            $this->assertTrue($this->newPhar->addFile('./composer.json', $newFileName));

            $this->assertTrue($this->newPhar->offsetExists($newFileName));
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
            $newFileName = 'test-file.txt';
            $newFileContent = 'This is a test file.';

            $this->assertTrue($this->newPhar->addFromString($newFileName, $newFileContent));

            $this->assertTrue($this->newPhar->offsetExists($newFileName));

            $this->assertStringEqualsFile("phar://{$this->pharPath}/{$newFileName}", $newFileContent);
        }

        public function testExtractToFromPhar()
        {
            $newFileName = 'test-file.txt';
            $newFileContent = 'This is a test file.';

            $this->assertTrue($this->newPhar->addFromString($newFileName, $newFileContent));

            $this->assertTrue($this->newPhar->offsetExists($newFileName));

            $this->assertTrue($this->newPhar->extractTo(base_dir(), $newFileName));

            $this->assertFileExists(base_dir() . DS . $newFileName, 'test-file was not extracted');

            $this->fs->remove(base_dir() . DS . $newFileName);
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