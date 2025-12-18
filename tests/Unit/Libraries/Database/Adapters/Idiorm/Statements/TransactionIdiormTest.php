<?php

namespace Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\IdiormDbalTestCase;
use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TransactionIdiormTest extends IdiormDbalTestCase
{

    public function testBeginTransactionAndRollback()
    {
        IdiormDbal::beginTransaction();

        IdiormDbal::execute('INSERT INTO profiles (firstname, lastname, age, country)
                                 VALUES (:firstname, :lastname, :age, :country)', [
            'firstname' => 'Alice',
            'lastname' => 'Smith',
            'age' => 30,
            'country' => 'Canada'
        ]);

        IdiormDbal::rollback();

        $result = IdiormDbal::query('SELECT * FROM profiles WHERE firstname = :firstname', ['firstname' => 'Alice']);

        $this->assertEmpty($result);
    }

    public function testBeginTransactionAndCommit()
    {
        IdiormDbal::beginTransaction();

        IdiormDbal::execute('INSERT INTO profiles (firstname, lastname, age, country)
                                 VALUES (:firstname, :lastname, :age, :country)', [
            'firstname' => 'Bob',
            'lastname' => 'Marley',
            'age' => 42,
            'country' => 'Jamaica'
        ]);

        IdiormDbal::commit();

        $result = IdiormDbal::query('SELECT * FROM profiles WHERE firstname = :firstname', ['firstname' => 'Bob']);

        $this->assertNotEmpty($result);

        $this->assertEquals('Marley', $result[0]['lastname']);
    }
}
