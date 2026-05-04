<?php

namespace Quantum\Tests\Unit\Migration\Templates;

use Quantum\Migration\Templates\MigrationTemplate;
use Quantum\Tests\Unit\AppTestCase;

class MigrationTemplateTest extends AppTestCase
{
    public function testCreateTemplateContainsExpectedOperations(): void
    {
        $template = MigrationTemplate::create('create_table_users_1001', 'users');

        $this->assertStringContainsString('use Quantum\Migration\Migration;', $template);
        $this->assertStringContainsString('class Create_table_users_1001 extends Migration', $template);
        $this->assertStringContainsString("\$tableFactory->create('users')", $template);
        $this->assertStringContainsString("\$tableFactory->drop('users')", $template);
    }

    public function testAlterTemplateContainsExpectedOperations(): void
    {
        $template = MigrationTemplate::alter('alter_table_users_1002', 'users');

        $this->assertStringContainsString('use Quantum\Migration\Migration;', $template);
        $this->assertStringContainsString('class Alter_table_users_1002 extends Migration', $template);
        $this->assertStringContainsString("\$tableFactory->get('users')", $template);
    }

    public function testRenameTemplateContainsExpectedOperations(): void
    {
        $template = MigrationTemplate::rename('rename_table_users_1003', 'users');

        $this->assertStringContainsString('use Quantum\Migration\Migration;', $template);
        $this->assertStringContainsString('class Rename_table_users_1003 extends Migration', $template);
        $this->assertStringContainsString("\$tableFactory->rename('users', \$newName)", $template);
        $this->assertStringContainsString("\$tableFactory->rename(\$newName, 'users')", $template);
    }

    public function testDropTemplateContainsExpectedOperations(): void
    {
        $template = MigrationTemplate::drop('drop_table_users_1004', 'users');

        $this->assertStringContainsString('use Quantum\Migration\Migration;', $template);
        $this->assertStringContainsString('class Drop_table_users_1004 extends Migration', $template);
        $this->assertStringContainsString("\$tableFactory->drop('users')", $template);
    }
}
