<?php

namespace Quantum\Tests\Libraries\Cache\Helpers;

use Quantum\Libraries\Cache\Cache;
use Quantum\Tests\AppTestCase;

class CacheHelperFunctionsTest extends AppTestCase
{

    public function testCacheHelper()
    {
        $this->assertInstanceOf(Cache::class, cache());
    }
}