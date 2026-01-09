<?php

namespace Libraries\Validation\Traits;

use Quantum\Libraries\Validation\Validator;
use Quantum\Libraries\Validation\Rule;
use Quantum\Tests\Unit\AppTestCase;

class LengthRuleTest extends AppTestCase
{
    public $validator;
    private $request;

    public function setUp(): void
    {
        parent::setUp();

        $this->validator = new Validator();

    }

    public function testRuleMinLen()
    {
        $this->validator->setRule('text', [
            Rule::minLen(3),
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'something']));

        $this->assertFalse($this->validator->isValid(['text' => 'so']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.minLen', $errors['text'][0]);
    }

    public function testRuleMaxLen()
    {
        $this->validator->setRule('text', [
            Rule::maxLen(5),
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'some']));

        $this->assertFalse($this->validator->isValid(['text' => 'something long']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.maxLen', $errors['text'][0]);
    }

    public function testRuleExactLen()
    {
        $this->validator->setRule('text', [
            Rule::exactLen(4),
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'some']));

        $this->assertFalse($this->validator->isValid(['text' => 'something long']));

        $this->assertFalse($this->validator->isValid(['text' => 'so']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.exactLen', $errors['text'][0]);
    }
}
