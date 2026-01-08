<?php

namespace Libraries\Validation\Traits;

use Quantum\Libraries\Validation\Validator;
use Quantum\Libraries\Validation\Rule;
use Quantum\Tests\Unit\AppTestCase;

class ResourceRuleTest extends AppTestCase
{

    public $validator;
    private $request;

    public function setUp(): void
    {
        parent::setUp();

        $this->validator = new Validator();

    }

    public function testRuleUrl()
    {
        $this->validator->setRule('text', [
            Rule::url()
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'http://something.com']));

        $this->assertTrue($this->validator->isValid(['text' => 'http://www.something.com']));

        $this->assertTrue($this->validator->isValid(['text' => 'http://subdomain.something.com']));

        $this->assertFalse($this->validator->isValid(['text' => 'something']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.url', $errors['text'][0]);
    }

    public function testRuleUrlExists()
    {
        $this->validator->setRule('text', [
            Rule::urlExists()
        ]);

        $this->assertTrue($this->validator->isValid(['text' => 'http://google.com']));

        $this->assertFalse($this->validator->isValid(['text' => 'http://someunregistereddomain.com']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.urlExists', $errors['text'][0]);
    }

    public function testRuleIp()
    {
        $this->validator->setRule('text', [
            Rule::ip(),
        ]);

        $this->assertTrue($this->validator->isValid(['text' => '127.0.0.1']));

        $this->assertFalse($this->validator->isValid(['text' => '521.652.125.987']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.ip', $errors['text'][0]);
    }

    public function testRuleIpV4()
    {
        $this->validator->setRule('text', [
            Rule::ipv4(),
        ]);

        $this->assertTrue($this->validator->isValid(['text' => '127.0.0.1']));

        $this->assertFalse($this->validator->isValid(['text' => '521.652.125.987']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.ipv4', $errors['text'][0]);
    }

    public function testRuleIpV6()
    {
        $this->validator->setRule('text', [
            Rule::ipv6()
        ]);

        $this->assertTrue($this->validator->isValid(['text' => '2001:0000:3238:DFE1:0063:0000:0000:FEFB']));

        $this->assertFalse($this->validator->isValid(['text' => '521.652.125.987']));

        $errors = $this->validator->getErrors();

        $this->assertNotEmpty($errors);

        $this->assertEquals('validation.ipv6', $errors['text'][0]);
    }
}