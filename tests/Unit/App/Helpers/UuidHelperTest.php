<?php

namespace App\Helpers;

use Quantum\Tests\Unit\AppTestCase;

class UuidHelperTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUuidRandomGeneratesValidUuidV4()
    {
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            uuid_random(),
            'uuid_random() should return a valid UUIDv4'
        );
    }

    public function testUuidOrderedGeneratesValidUuidV1()
    {
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            uuid_ordered(),
            'uuid_ordered() should return a valid UUIDv1'
        );
    }

    public function testUuidRandomIsUnique()
    {
        $this->assertNotEquals(uuid_random(), uuid_random(), 'Two random UUIDs should be different');
    }

    public function testUuidOrderedIsChronologicallyIncreasing()
    {
        $uuid1 = uuid_ordered();
        usleep(10); // short delay
        $uuid2 = uuid_ordered();

        $this->assertLessThan(
            0,
            strcmp($uuid1, $uuid2),
            'UUIDv1 should be lexicographically increasing'
        );
    }
}