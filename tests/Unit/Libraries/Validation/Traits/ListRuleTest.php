<?php

namespace Libraries\Validation\Traits;

use Quantum\Libraries\Validation\Validator;
use Quantum\Libraries\Validation\Rule;
use Quantum\Tests\Unit\AppTestCase;

class ListRuleTest extends AppTestCase
{

    private $request;

    public function setUp(): void
    {
        parent::setUp();


        $this->validator = new Validator();

    }

    public function testRuleContains()
    {
        $this->validator->setRule('text', [
            Rule::contains('Fisherman goes out')
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'Fisherman']));

        $this->assertTrue($this->validator->isValid(['text' => 'fish']));

        $this->assertTrue($this->validator->isValid(['text' => 'out']));

        $this->assertFalse($this->validator->isValid(['text' => 'comes']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.contains', $errors['text'][0]);
    }

    public function testRuleContainsList()
    {
        $this->validator->setRule('text', [
            Rule::containsList('male', 'female')
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'male']));

        $this->assertTrue($this->validator->isValid(['text' => 'female']));

        $this->assertFalse($this->validator->isValid(['text' => 'other']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.containsList', $errors['text'][0]);
    }

    public function testRuleDoesntContainsList()
    {
        $this->validator->setRule('text', [
            Rule::doesntContainsList('male', 'famale')
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'elephant']));

        $this->assertTrue($this->validator->isValid(['text' => 'other']));

        $this->assertFalse($this->validator->isValid(['text' => 'famale']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.doesntContainsList', $errors['text'][0]);
    }
}