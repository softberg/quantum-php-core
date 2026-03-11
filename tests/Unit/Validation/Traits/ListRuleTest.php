<?php

namespace Quantum\Tests\Unit\Validation\Traits;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Validation\Validator;
use Quantum\Validation\Rule;

class ListRuleTest extends AppTestCase
{
    public Validator $validator;

    public function setUp(): void
    {
        parent::setUp();

        $this->validator = new Validator();

    }

    public function testRuleContains(): void
    {
        $this->validator->setRule('text', [
            Rule::contains('Fisherman goes out'),
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'Fisherman']));

        $this->assertTrue($this->validator->isValid(['text' => 'fish']));

        $this->assertTrue($this->validator->isValid(['text' => 'out']));

        $this->assertFalse($this->validator->isValid(['text' => 'comes']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.contains', $errors['text'][0]);
    }

    public function testRuleContainsList(): void
    {
        $this->validator->setRule('text', [
            Rule::containsList('male', 'female'),
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'male']));

        $this->assertTrue($this->validator->isValid(['text' => 'female']));

        $this->assertFalse($this->validator->isValid(['text' => 'other']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.containsList', $errors['text'][0]);
    }

    public function testRuleDoesntContainsList(): void
    {
        $this->validator->setRule('text', [
            Rule::doesntContainsList('male', 'famale'),
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'elephant']));

        $this->assertTrue($this->validator->isValid(['text' => 'other']));

        $this->assertFalse($this->validator->isValid(['text' => 'famale']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.doesntContainsList', $errors['text'][0]);
    }
}
