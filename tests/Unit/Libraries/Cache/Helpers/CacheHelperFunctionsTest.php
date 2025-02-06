<?php

namespace Quantum\Tests\Unit\Libraries\Cache\Helpers;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Libraries\Cache\Cache;

class CacheHelperFunctionsTest extends AppTestCase
{

    public function testCacheHelper()
    {
        $this->assertInstanceOf(Cache::class, cache());
    }
}