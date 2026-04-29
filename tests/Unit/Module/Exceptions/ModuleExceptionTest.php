<?php

namespace Quantum\Tests\Unit\Module\Exceptions;

use Quantum\Module\Exceptions\ModuleException;
use Quantum\Tests\Unit\AppTestCase;

class ModuleExceptionTest extends AppTestCase
{
    public function testModuleRoutesNotFound(): void
    {
        $e = ModuleException::moduleRoutesNotFound('Blog');
        $this->assertSame('Routes not found for module `Blog`', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }

    public function testModuleConfigNotFound(): void
    {
        $e = ModuleException::moduleConfigNotFound();
        $this->assertSame('Config not found for a module', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }

    public function testModuleCreationIncomplete(): void
    {
        $e = ModuleException::moduleCreationIncomplete();
        $this->assertSame('Module creation incomplete: missing files.', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }

    public function testMissingModuleTemplate(): void
    {
        $e = ModuleException::missingModuleTemplate('demo');
        $this->assertSame('Template `demo` does not exist', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }

    public function testMissingModuleDirectory(): void
    {
        $e = ModuleException::missingModuleDirectory();
        $this->assertSame('Module directory does not exist, skipping config update.', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }

    public function testModuleAlreadyExists(): void
    {
        $e = ModuleException::moduleAlreadyExists('Admin');
        $this->assertSame('A module or prefix named `Admin` already exists', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }

    public function testDirectoryListingFailed(): void
    {
        $e = ModuleException::directoryListingFailed('/tmp/src');
        $this->assertSame('Failed to list directory `/tmp/src`', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }
}
