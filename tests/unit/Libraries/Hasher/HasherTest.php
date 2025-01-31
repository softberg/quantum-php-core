<?php

namespace Quantum\Tests\Libraries\Hasher;

use Quantum\Libraries\Hasher\Hasher;
use PHPUnit\Framework\TestCase;

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

        $this->hasher->setAlgorithm(PASSWORD_DEFAULT);

        $this->assertEquals(PASSWORD_DEFAULT, $this->hasher->getAlgorithm());
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
        $this->hasher->setCost(10);

        $hashed = $this->hasher->hash($this->password);

        $this->assertFalse($this->hasher->needsRehash($hashed));

        $this->hasher->setCost(12);

        $this->assertTrue($this->hasher->needsRehash($hashed));

        $this->hasher->setAlgorithm(PASSWORD_DEFAULT)->setCost(11);

        $this->assertTrue($this->hasher->needsRehash($hashed));
    }

    public function testInfo()
    {
        $hashed = $this->hasher->hash($this->password);

        $this->assertIsArray($this->hasher->info($hashed));

        $this->assertArrayHasKey('algoName', $this->hasher->info($hashed));
    }
}