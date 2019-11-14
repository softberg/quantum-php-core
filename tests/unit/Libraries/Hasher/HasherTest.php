<?php

namespace Quantum\Test\Unit;

use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Hasher\Hasher;

class HasherTest extends TestCase
{

    private $hasher;
    private $password = 'plaintext';
    private $otherPassword = 'other';

    public function setUp(): void
    {
        $this->hasher = new Hasher();
    }

    public function testSetAndGetAlgorithm()
    {
        $this->assertEquals(PASSWORD_BCRYPT, $this->hasher->getAlgorithm());

        $this->hasher->setAlgorithm(PASSWORD_ARGON2I);

        $this->assertEquals(PASSWORD_ARGON2I, $this->hasher->getAlgorithm());
    }

    public function testSetAndGetCost()
    {
        $this->assertEquals(12, $this->hasher->getCost());

        $this->hasher->setCost(14);

        $this->assertEquals(14, $this->hasher->getCost());
    }

    public function testHashAndCheck()
    {
        $hashed = $this->hasher->hash($this->password);

        $this->assertTrue($this->hasher->check($this->password, $hashed));

        $this->assertFalse($this->hasher->check($this->otherPassword, $hashed));
    }

    public function testNeedsRehash()
    {
        $hashed = $this->hasher->hash($this->password);

        $this->assertTrue($this->hasher->needsRehash($hashed));

        $this->hasher->setAlgorithm(PASSWORD_ARGON2I)->setCost(11);

        $this->assertTrue($this->hasher->needsRehash($hashed));
    }

    public function testInfo()
    {
        $hashed = $this->hasher->hash($this->password);

        $this->assertIsArray($this->hasher->info($hashed));

        $this->assertArrayHasKey('algoName', $this->hasher->info($hashed));
    }

}
