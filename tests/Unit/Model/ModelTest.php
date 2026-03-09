<?php

namespace Quantum\Tests\Unit\Model;

use Quantum\Model\Exceptions\ModelException;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Model\Model;

class ModelTest extends AppTestCase
{
    private TestPlainModel $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = new TestPlainModel();
    }

    public function testModelInstance(): void
    {
        $this->assertInstanceOf(Model::class, $this->model);
    }

    public function testModelPropSetterAndGetter(): void
    {
        $this->assertNull($this->model->prop('firstname'));

        $this->model->prop('firstname', 'John');

        $this->assertEquals('John', $this->model->prop('firstname'));
    }

    public function testModelPropReturnsSelfWhenSettingValue(): void
    {
        $result = $this->model->prop('lastname', 'Doe');

        $this->assertSame($this->model, $result);
    }

    public function testModelFill(): void
    {
        $this->model->fill([
            'firstname' => 'Jane',
            'lastname' => 'Due',
            'age' => 35,
        ]);

        $this->assertEquals('Jane', $this->model->firstname);
        $this->assertEquals('Due', $this->model->lastname);
        $this->assertEquals(35, $this->model->age);
    }

    public function testModelFillWithUndefinedFillable(): void
    {
        $this->expectException(ModelException::class);

        $this->expectExceptionMessage('Inappropriate property `country` for fillable object');

        $this->model->fill([
            'country' => 'Ireland',
        ]);
    }

    public function testModelAsArrayReturnsAttributes(): void
    {
        $this->model->prop('firstname', 'John');
        $this->model->prop('lastname', 'Doe');
        $this->model->prop('age', 45);

        $data = $this->model->asArray();

        $this->assertIsArray($data);

        $this->assertEquals('John', $data['firstname']);
        $this->assertEquals('Doe', $data['lastname']);
        $this->assertEquals(45, $data['age']);
    }

    public function testModelAsArrayHidesHiddenFields(): void
    {
        $this->model->prop('firstname', 'John');
        $this->model->prop('lastname', 'Doe');
        $this->model->prop('password', 'secret');

        $this->model->hidden = ['password'];

        $data = $this->model->asArray();

        $this->assertIsArray($data);

        $this->assertArrayHasKey('firstname', $data);
        $this->assertArrayHasKey('lastname', $data);

        $this->assertArrayNotHasKey('password', $data);
    }

    public function testModelIsEmptyReturnsTrue(): void
    {
        $this->assertTrue($this->model->isEmpty());
    }

    public function testModelIsEmptyReturnsFalse(): void
    {
        $this->model->prop('firstname', 'John');

        $this->assertFalse($this->model->isEmpty());
    }

    public function testModelMagicGetterAndSetter(): void
    {
        $this->assertNull($this->model->undefinedProperty);

        $this->model->undefinedProperty = 'Something';

        $this->assertEquals('Something', $this->model->undefinedProperty);
    }

    public function testModelMagicIsset(): void
    {
        $this->assertFalse(isset($this->model->firstname));

        $this->model->firstname = 'John';

        $this->assertTrue(isset($this->model->firstname));
    }

    public function testModelMagicUnset(): void
    {
        $this->model->firstname = 'John';

        $this->assertTrue(isset($this->model->firstname));

        unset($this->model->firstname);

        $this->assertFalse(isset($this->model->firstname));

        $this->assertNull($this->model->firstname);
    }
}

/**
 * Concrete model stub for testing Quantum\Model\Model
 */
class TestPlainModel extends Model
{
    protected array $fillable = [
        'firstname',
        'lastname',
        'age',
    ];
}
