<?php

namespace Quantum\Tests\Libraries\Database\Schema;

use Quantum\Libraries\Database\Constants\Type;
use Quantum\Libraries\Database\Schemas\Column;
use Quantum\Libraries\Database\Constants\Key;
use Quantum\Libraries\Database\Schemas\Table;
use PHPUnit\Framework\TestCase;
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

    public function testAddColumnWithAttributes()
    {
        $table = new Table('test');
        $table->setAction(Table::CREATE);
        $table->addColumn('id', Type::INT, 11)->autoIncrement()->attribute(Column::ATTR_UNSIGNED);

        $expectedSql = 'CREATE TABLE `test` (`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`));';

        $this->assertEquals($expectedSql, $table->getSql());
    }

    public function testAddColumnWithIndexs()
    {
        $table = new Table('test');
        $table->setAction(Table::CREATE);
        $table->addColumn('profile_id', Type::INT, 11)->index();
        $table->addColumn('username', Type::INT, 11)->unique();
        $table->addColumn('email', Type::INT, 11)->unique();

        $expectedSql = 'CREATE TABLE `test` (`profile_id` INT(11) NOT NULL, `username` INT(11) NOT NULL, `email` INT(11) NOT NULL, INDEX (`profile_id`), UNIQUE (`username`), UNIQUE (`email`));';

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

    public function testModifyColumnWithAttributes()
    {
        $table = new Table('test');
        $table->setAction(Table::ALTER);
        $table->modifyColumn('name', Type::VARCHAR, 100)->default('New user')->comment('User nikname');

        $expectedSql = 'ALTER TABLE `test` MODIFY COLUMN `name` VARCHAR(100) NOT NULL DEFAULT \'New user\' COMMENT \'User nikname\';';

        $this->assertEquals($expectedSql, $table->getSql());
    }

    public function testAddColumnAndModifyColumn()
    {
        $table = new Table('test');
        $table->setAction(Table::ALTER);
        $table->addColumn('surename', Type::VARCHAR, 50);
        $table->modifyColumn('profession', Type::ENUM, ['plumber', 'driver', 'security'])->default('driver');

        $expectedSql = 'ALTER TABLE `test` ADD COLUMN `surename` VARCHAR(50) NOT NULL, MODIFY COLUMN `profession` ENUM(\'plumber\', \'driver\', \'security\') NOT NULL DEFAULT \'driver\';';

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

    public function testDropColumn()
    {
        $table = new Table('test');
        $table->setAction(Table::ALTER);
        $table->dropColumn('courses');

        $expectedSql = 'ALTER TABLE `test` DROP COLUMN `courses`;';

        $this->assertEquals($expectedSql, $table->getSql());
    }

    public function testAddIndex()
    {
        $table = new Table('test');
        $table->setAction(Table::ALTER);
        $table->addIndex('name', Key::INDEX);

        $expectedSql = 'ALTER TABLE `test` ADD INDEX (`name`);';

        $this->assertEquals($expectedSql, $table->getSql());
    }

    public function testAddIndexWithName()
    {
        $table = new Table('test');
        $table->setAction(Table::ALTER);
        $table->addIndex('name', Key::UNIQUE, 'idx_name');

        $expectedSql = 'ALTER TABLE `test` ADD UNIQUE `idx_name` (`name`);';

        $this->assertEquals($expectedSql, $table->getSql());
    }

    public function testAddingMultipleIndexes()
    {
        $table = new Table('test');
        $table->setAction(Table::ALTER);
        $table->addIndex('email', Key::UNIQUE, 'idx_email');
        $table->addIndex('username', Key::INDEX, 'idx_username');

        $expectedSql = 'ALTER TABLE `test` ADD INDEX `idx_username` (`username`), ADD UNIQUE `idx_email` (`email`);';

        $this->assertEquals($expectedSql, $table->getSql());
    }

    public function testDropIndex()
    {
        $table = new Table('test');
        $table->setAction(Table::ALTER);
        $table->dropIndex('idx_name');

        $expectedSql = 'ALTER TABLE `test` DROP INDEX `idx_name`;';

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
