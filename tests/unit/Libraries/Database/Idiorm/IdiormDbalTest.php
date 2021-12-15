<?php

namespace Quantum\Test\Unit {

    require_once __DIR__ . DS . 'IdiormDbalTestCase.php';

    use Quantum\Libraries\Database\Idiorm\IdiormDbal;


    class IdiormDbalTest extends IdiormDbalTestCase
    {
        public function testIdiormConstructor()
        {
            $userModel = new IdiormDbal('users');

            $this->assertInstanceOf(IdiormDbal::class, $userModel);
        }

        public function testIdiormGetTable()
        {
            $userModel = new IdiormDbal('users');

            $this->assertEquals('users', $userModel->getTable());
        }
    }

}
