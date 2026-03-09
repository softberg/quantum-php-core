<?php

namespace Quantum\Tests\Unit\Validation;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Validation\Validator;
use Quantum\Validation\Rule;

class ValidatorTest extends AppTestCase
{
    private Validator $validator;

    public function setUp(): void
    {
        parent::setUp();

        $this->validator = new Validator();
    }

    public function testValidatorConstructor(): void
    {
        $this->assertInstanceOf(Validator::class, $this->validator);
    }

    public function testAddUpdateDeleteRule(): void
    {
        $this->validator->setRule('text', [
            Rule::minLen(5),
            Rule::maxLen(10),
        ]);

        $this->assertFalse($this->validator->isValid(['text' => 'some']));

        $this->validator->updateRule('text', Rule::minLen(2));

        $this->validator->flushErrors();

        $this->assertTrue($this->validator->isValid(['text' => 'some']));

        $this->assertFalse($this->validator->isValid(['text' => 'some long text goes']));

        $this->validator->deleteRule('text', 'maxLen');

        $this->validator->flushErrors();

        $this->assertTrue($this->validator->isValid(['text' => 'some long text goes']));
    }

    public function testMultipleRules(): void
    {
        $this->validator->setRule('text', [
            Rule::minLen(7),
            Rule::maxLen(20),
            Rule::email(),
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'someone@gmail.com']));

        $this->assertFalse($this->validator->isValid(['text' => 'someonegmail.com']));

        $errors = $this->validator->getErrors();

        $this->assertEquals('validation.email', $errors['text'][0]);

        $this->assertFalse($this->validator->isValid(['text' => 'a@c.cc']));

        $errors = $this->validator->getErrors();

        $this->assertEquals('validation.minLen', $errors['text'][0]);

        $this->assertFalse($this->validator->isValid(['text' => 'verylongemailaddress@longdomain.cc']));

        $errors = $this->validator->getErrors();

        $this->assertEquals('validation.maxLen', $errors['text'][0]);
    }

    public function testMultipleFields(): void
    {
        $this->validator->setRules([
            'name' => [
                Rule::required(),
                Rule::maxLen(30),
            ],
            'email' => [
                Rule::required(),
                Rule::email(),
            ],
            'age' => [
                Rule::required(),
                Rule::minNumeric(16),
            ],
        ]);

        $data = [
            'name' => 'John',
            'email' => 'john@gmail.com',
            'age' => 36,
        ];

        $this->assertTrue($this->validator->isValid($data));

        $data = [
            'name' => 'Junior',
            'email' => 'johny@gc.com',
            'age' => 12,
        ];

        $this->assertFalse($this->validator->isValid($data));

        $this->validator->flushRules();

        $data = [
            'name' => '',
            'email' => 'john@gmail.com',
            'age' => 0,
        ];

        $this->assertTrue($this->validator->isValid($data));

        $this->validator->setRules([
            'phone' => [
                Rule::required(),
                Rule::minLen(8),
            ],
        ]);

        $data = [
            'name' => 'John',
            'email' => 'john@gmail.com',
            'age' => 36,
        ];

        $this->assertFalse($this->validator->isValid($data));
    }

    public function testDifferentMultipleFields(): void
    {
        $this->validator->setRules([
            'name' => [
                Rule::maxLen(30),
            ],
            'email' => [
                Rule::required(),
                Rule::email(),
            ],
            'age' => [
                Rule::required(),
                Rule::minNumeric(16),
            ],
        ]);

        $data = [
            'email' => 'john@gmail.com',
            'age' => 36,
        ];

        $this->assertTrue($this->validator->isValid($data));

        $data = [
            'name' => '',
            'email' => 'john@gmail.com',
            'age' => 36,
        ];

        $this->assertTrue($this->validator->isValid($data));
    }

    public function testCustomValidator(): void
    {
        $this->validator->addRule('checkName', fn ($value, $param = null): bool => $value == $param);

        $this->validator->setRules([
            'name' => [
                Rule::checkName('Wayne'),
            ],
        ]);

        $this->assertTrue($this->validator->isValid(['name' => 'Wayne']));

        $this->assertFalse($this->validator->isValid(['name' => 'John']));
    }
}
