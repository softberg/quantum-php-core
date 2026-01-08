<?php

namespace Libraries\Validation\Traits;

use Quantum\Libraries\Validation\Validator;
use Quantum\Libraries\Validation\Rule;
use Quantum\Tests\Unit\AppTestCase;

class TypeRuleTest extends AppTestCase
{

    public $validator;
    public function setUp(): void
    {
        parent::setUp();

        $this->validator = new Validator();
    }

    public function testRuleAlpha()
    {
        $this->validator->setRule('text', [
            Rule::alpha()
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'some']));

        $this->assertFalse($this->validator->isValid(['text' => 123]));

        $this->assertFalse($this->validator->isValid(['text' => 'so 456']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.alpha', $errors['text'][0]);
    }

    public function testRuleAlphaNumeric()
    {
        $this->validator->setRule('text', [
            Rule::alphaNumeric()
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'some']));

        $this->assertTrue($this->validator->isValid(['text' => 'num456']));

        $this->assertTrue($this->validator->isValid(['text' => 123]));

        $this->assertFalse($this->validator->isValid(['text' => '*- ']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.alphaNumeric', $errors['text'][0]);
    }

    public function testRuleAlphaDash()
    {
        $this->validator->setRule('text', [
            Rule::alphaDash()
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'some']));

        $this->assertTrue($this->validator->isValid(['text' => 'num-']));

        $this->assertFalse($this->validator->isValid(['text' => '55-']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.alphaDash', $errors['text'][0]);
    }

    public function testRuleAlphaSpace()
    {
        $this->validator->setRule('text', [
            Rule::alphaSpace()
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'some']));

        $this->assertTrue($this->validator->isValid(['text' => 'num555']));

        $this->assertTrue($this->validator->isValid(['text' => 'num 555']));

        $this->assertFalse($this->validator->isValid(['text' => 'num: 555']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.alphaSpace', $errors['text'][0]);
    }

    public function testRuleNumeric()
    {
        $this->validator->setRule('text', [
            Rule::numeric()
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 555]));

        $this->assertTrue($this->validator->isValid(['text' => '555']));

        $this->assertFalse($this->validator->isValid(['text' => 'num 555']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.numeric', $errors['text'][0]);
    }

    public function testRuleInteger()
    {
        $this->validator->setRule('text', [
            Rule::integer()
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 555]));

        $this->assertTrue($this->validator->isValid(['text' => '555']));

        $this->assertFalse($this->validator->isValid(['text' => 555.12]));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.integer', $errors['text'][0]);
    }

    public function testRuleFloat()
    {
        $this->validator->setRule('text', [
            Rule::float()
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 11.12]));

        $this->assertTrue($this->validator->isValid(['text' => '11.12']));

        $this->assertTrue($this->validator->isValid(['text' => 11]));

        $this->assertFalse($this->validator->isValid(['text' => 'something']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.float', $errors['text'][0]);
    }

    public function testRuleBoolean()
    {
        $this->validator->setRule('text', [
            Rule::boolean()
        ]);

        $this->assertTrue($this->validator->isValid(['text' => true]));

        $this->assertTrue($this->validator->isValid(['text' => 'true']));

        $this->assertTrue($this->validator->isValid(['text' => 1]));

        $this->assertTrue($this->validator->isValid(['text' => 0]));

        $this->assertTrue($this->validator->isValid(['text' => 'false']));

        $this->assertTrue($this->validator->isValid(['text' => false]));

        $this->assertFalse($this->validator->isValid(['text' => 'something']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.boolean', $errors['text'][0]);
    }

    public function testRuleMinNumeric()
    {
        $this->validator->setRule('text', [
            Rule::minNumeric(20)
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 25]));

        $this->assertFalse($this->validator->isValid(['text' => 15]));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.minNumeric', $errors['text'][0]);
    }

    public function testRuleMaxNumeric()
    {
        $this->validator->setRule('text', [
            Rule::maxNumeric(20)
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 15]));

        $this->assertFalse($this->validator->isValid(['text' => 25]));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.maxNumeric', $errors['text'][0]);
    }
}