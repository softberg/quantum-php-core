<?php

namespace Quantum\Tests\Unit\Archive\Factories;

use Quantum\Archive\Exceptions\ArchiveException;
use Quantum\Archive\Contracts\ArchiveInterface;
use Quantum\Archive\Factories\ArchiveFactory;
use Quantum\Archive\Adapters\PharAdapter;
use Quantum\Archive\Adapters\ZipAdapter;
use Quantum\Archive\Archive;
use Quantum\Tests\Unit\AppTestCase;

class ArchiveFactoryTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testArchiveFactoryInstance()
    {
        $archive = ArchiveFactory::get();

        $this->assertInstanceOf(Archive::class, $archive);
    }

    public function testArchiveFactoryPharAdapter()
    {
        $archive = ArchiveFactory::get();

        $this->assertInstanceOf(PharAdapter::class, $archive->getAdapter());

        $this->assertInstanceOf(ArchiveInterface::class, $archive->getAdapter());
    }

    public function testArchiveFactoryZipAdapter()
    {
        $archive = ArchiveFactory::get(Archive::ZIP);

        $this->assertInstanceOf(ZipAdapter::class, $archive->getAdapter());

        $this->assertInstanceOf(ArchiveInterface::class, $archive->getAdapter());
    }

    public function testArchiveFactoryInvalidAdapter()
    {
        $this->expectException(ArchiveException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported');

        ArchiveFactory::get('invalid_type');
    }

    public function testArchiveFactoryReturnsSameInstance()
    {
        $archive1 = ArchiveFactory::get();
        $archive2 = ArchiveFactory::get();

        $this->assertSame($archive1, $archive2);
    }
}
