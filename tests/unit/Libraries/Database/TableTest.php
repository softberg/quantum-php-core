<?php

namespace Quantum\Tests\Libraries\Database;

use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Database\Table;
use Quantum\Libraries\Database\Type;
use Mockery;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TableTest extends TestCase
{

    private $db;

    public function setUp(): void
    {
        $this->db = Mockery::mock('overload:Quantum\Libraries\Database\Database');

        $this->db->shouldReceive('execute')->andReturn(true);
    }

    public function testSetActionAndGetQuery()
    {
        $table = new Table('test');
        $table->setAction(Table::RENAME, ['newName' => 'general']);

        $expectedSql = 'RENAME TABLE `test` TO `general`;';

        $this->assertEquals($expectedSql, $table->getSql());
    }

    public function testTableConstructor()
    {
        $this->assertInstanceOf(Table::class, new Table('test'));
    }

    public function testAddColumn()
    {
        $table = new Table('test');
        $table->setAction(Table::CREATE);
        $table->addColumn('id', Type::INT, 11);

        $expectedSql = 'CREATE TABLE `test` (`id` INT(11) NOT NULL);';

        $this->assertEquals($expectedSql, $table->getSql());
    }

    public function testModifyColumn()
    {
        $table = new Table('test');
        $table->setAction(Table::ALTER);
        $table->modifyColumn('name', Type::VARCHAR, 50);

        $expectedSql = 'ALTER TABLE `test` MODIFY COLUMN `name` VARCHAR(50) NOT NULL;';

        $this->assertEquals($expectedSql, $table->getSql());
    }

    public function testRenameColumn()
    {
        $table = new Table('test');
        $table->setAction(Table::ALTER);
        $table->renameColumn('courses', 'lessons');

        $expectedSql = 'ALTER TABLE `test` RENAME COLUMN `courses` TO `lessons`;';

        $this->assertEquals($expectedSql, $table->getSql());
    }

    public function testDropeColumn()
    {
        $table = new Table('test');
        $table->setAction(Table::ALTER);
        $table->dropColumn('courses');

        $expectedSql = 'ALTER TABLE `test` DROP COLUMN `courses`;';

        $this->assertEquals($expectedSql, $table->getSql());
    }

    public function testAfterColumn()
    {
        $table = new Table('test');
        $table->setAction(Table::ALTER);
        $table->addColumn('profile_id', Type::INT, 11)->after('id');

        $expectedSql = 'ALTER TABLE `test` ADD COLUMN `profile_id` INT(11) NOT NULL AFTER `id`;';

        $this->assertEquals($expectedSql, $table->getSql());
    }

}
