<?php

namespace Quantum\Tests\Libraries\Database;

use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Database\Column;
use Quantum\Libraries\Database\Type;

class ColumnTest extends TestCase
{

    public function testColumnConstructor()
    {
        $this->assertInstanceOf(Column::class, new Column('test'));
    }

    public function testColumnRename()
    {
        $column = new Column('test', Type::INT, 11);

        $column->renameTo('new_test');

        $this->assertEquals('new_test', $column->get('newName'));
    }

    public function testColumnGet()
    {
        $column = new Column('test', Type::INT, 11);

        $this->assertEquals('test', $column->get(Column::NAME));

        $this->assertEquals('INT', $column->get(Column::TYPE));

        $this->assertEquals(11, $column->get(Column::CONSTRAINT));
    }

    public function testColumnAutoIncrement()
    {
        $column = new Column('test', Type::INT, 11);

        $column->autoIncrement();

        $this->assertEquals('AUTO_INCREMENT', $column->get(Column::AUTO_INCREMENT));

        $this->assertEquals('primary', $column->get('indexKey'));
    }

    public function testColumnPrimaryKey()
    {
        $column = new Column('test', Type::INT, 11);

        $column->primary();

        $this->assertEquals('primary', $column->get('indexKey'));
    }

    public function testColumnIndexKey()
    {
        $column = new Column('test', Type::INT, 11);

        $column->index();

        $this->assertEquals('index', $column->get('indexKey'));
    }

    public function testColumnUniqueKey()
    {
        $column = new Column('test', Type::INT, 11);

        $column->unique();

        $this->assertEquals('unique', $column->get('indexKey'));
    }

    public function testColumnFulltextKey()
    {
        $column = new Column('test', Type::INT, 11);

        $column->fulltext();

        $this->assertEquals('fulltext', $column->get('indexKey'));
    }

    public function testColumnSpatialKey()
    {
        $column = new Column('test', Type::INT, 11);

        $column->spatial();

        $this->assertEquals('spatial', $column->get('indexKey'));
    }

    public function testAddTypeToColumn()
    {
        $column = new Column('test');

        $column->type(Type::VARCHAR, 255);

        $this->assertEquals('VARCHAR', $column->get(Column::TYPE));

        $this->assertEquals(255, $column->get(Column::CONSTRAINT));

        $column->type(Type::BOOL);

        $this->assertEquals('BOOL', $column->get(Column::TYPE));
    }

    public function testAddOrRemoveColumnNullableProperty()
    {
        $column = new Column('test', Type::VARCHAR, 255);

        $this->assertEquals('NOT NULL', $column->get(Column::NULLABLE));

        $column->nullable();

        $this->assertEquals('NULL', $column->get(Column::NULLABLE));

        $column->nullable(false);

        $this->assertEquals('NOT NULL', $column->get(Column::NULLABLE));
    }

    public function testAddOrRemoveColumnDefaultValue()
    {
        $column = new Column('test', Type::INT, 11);

        $this->assertNull($column->get(Column::DEFAULT));

        $column->default(5);

        $this->assertEquals(5, $column->get(Column::DEFAULT));

        $column->default(null);

        $this->assertNull($column->get(Column::DEFAULT));
    }

    public function testAddOrRemoveColumnAttribute()
    {
        $column = new Column('test', Type::INT, 11);

        $this->assertNull($column->get(Column::ATTRIBUTE));

        $column->attribute(Column::ATTR_BINARY);

        $this->assertEquals('BINARY', $column->get(Column::ATTRIBUTE));

        $column->attribute(Column::ATTR_UNSIGNED);

        $this->assertEquals('UNSIGNED', $column->get(Column::ATTRIBUTE));

        $column->attribute(null);

        $this->assertNull($column->get(Column::ATTRIBUTE));
    }

    public function testAddOrRemoveColumnComment()
    {
        $column = new Column('test', Type::INT, 11);

        $this->assertNull($column->get(Column::COMMENT));

        $column->comment('This is test column');

        $this->assertEquals('This is test column', $column->get(Column::COMMENT));

        $column->comment(null);

        $this->assertNull($column->get(Column::COMMENT));
    }

    public function testAfterColumn()
    {
        $column = new Column('test', Type::INT, 11);

        $column->after('prev');

        $this->assertEquals('prev', $column->get(Column::AFTER));
    }

}
