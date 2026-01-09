<?php

namespace Quantum\Tests\Unit\Libraries\Archive;

use Quantum\Libraries\Archive\Exceptions\ArchiveException;
use Quantum\Libraries\Archive\Contracts\ArchiveInterface;
use Quantum\Libraries\Archive\Adapters\PharAdapter;
use Quantum\Libraries\Archive\Adapters\ZipAdapter;
use Quantum\Libraries\Archive\Archive;
use Quantum\Tests\Unit\AppTestCase;

class ArchiveTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testArchiveGetAdapter()
    {
        $archive = new Archive(new PharAdapter());

        $this->assertInstanceOf(PharAdapter::class, $archive->getAdapter());

        $this->assertInstanceOf(ArchiveInterface::class, $archive->getAdapter());

        $archive = new Archive(new ZipAdapter());

        $this->assertInstanceOf(ZipAdapter::class, $archive->getAdapter());

        $this->assertInstanceOf(ArchiveInterface::class, $archive->getAdapter());
    }

    public function testArchiveCallingValidMethod()
    {
        $pharAdapter = new PharAdapter();
        $pharAdapter->setName('test.phar');

        $archive = new Archive($pharAdapter);

        $this->assertIsInt($archive->count());

        $this->assertEquals(0, $archive->count());
    }

    public function testArchiveCallingInvalidMethod()
    {
        $pharAdapter = new PharAdapter();
        $pharAdapter->setName('test.phar');

        $archive = new Archive($pharAdapter);

        $this->expectException(ArchiveException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `' . PharAdapter::class . '`');

        $archive->callingInvalidMethod();
    }
}
