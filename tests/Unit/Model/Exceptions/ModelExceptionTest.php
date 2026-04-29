<?php

namespace Quantum\Tests\Unit\Model\Exceptions;

use Quantum\Model\Exceptions\ModelException;
use Quantum\Tests\Unit\AppTestCase;

class ModelExceptionTest extends AppTestCase
{
    public function testInappropriateProperty(): void
    {
        $e = ModelException::inappropriateProperty('foo');
        $this->assertSame('Inappropriate property `foo` for fillable object', $e->getMessage());
        $this->assertSame(E_WARNING, $e->getCode());
    }

    public function testWrongRelation(): void
    {
        $e = ModelException::wrongRelation('User', 'posts');
        $this->assertSame('The model `User` does not define relation with `posts`', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }

    public function testRelationTypeMissing(): void
    {
        $e = ModelException::relationTypeMissing('User', 'Post');
        $this->assertSame('Relation type is missing for model `User` with related model `Post`', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }

    public function testMissingRelationKeys(): void
    {
        $e = ModelException::missingRelationKeys('User', 'Post');
        $this->assertSame('Relation keys `foreign_key` or `local_key` are missing for model `User` with related model `Post`', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }

    public function testMissingForeignKeyValue(): void
    {
        $e = ModelException::missingForeignKeyValue('User', 'profile_id');
        $this->assertSame('Foreign key `profile_id` is missing in model `User`', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }

    public function testUnsupportedRelationType(): void
    {
        $e = ModelException::unsupportedRelationType('foo');
        $this->assertSame('Relation type `foo` is not supported', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }

    public function testOrmIsNotSet(): void
    {
        $e = ModelException::ormIsNotSet();
        $this->assertSame('ORM instance not initialized', $e->getMessage());
        $this->assertSame(E_ERROR, $e->getCode());
    }
}
