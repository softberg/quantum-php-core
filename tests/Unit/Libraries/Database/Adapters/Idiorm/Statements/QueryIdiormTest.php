<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\IdiormDbalTestCase;
use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class QueryIdiormTest extends IdiormDbalTestCase
{

    public function testIdiormExecute()
    {
        $eventModel = new IdiormDbal('events');

        $event = $eventModel->findOne(1);

        $this->assertEquals('Dance', $event->prop('title'));

        $eventModel->execute('UPDATE events SET title=:title WHERE id=:id', ['title' => 'Singing', 'id' => 1]);

        $event = $eventModel->findOne(1);

        $this->assertEquals('Singing', $event->prop('title'));
    }

    public function testIdiormQuery()
    {
        $events = IdiormDbal::query('SELECT * FROM events WHERE started_at BETWEEN :date_from AND :date_to', ['date_from' => '2035-02-14 10:15:12', 'date_to' => '2045-02-14 10:15:12']);

        $this->assertEquals('Film', $events[0]['title']);

        $this->assertEquals('Ireland', $events[0]['country']);

        $this->assertEquals('2040-02-14 10:15:12', $events[0]['started_at']);
    }

    /** Works only if debug set to true */
    public function testIdiormLastQuery()
    {
        IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

        $eventModel = new IdiormDbal('events');

        $eventModel->criteria('country', '=', 'Ireland')->get();

        $this->assertEquals("SELECT * FROM `events` WHERE `country` = 'Ireland'", IdiormDbal::lastQuery());
    }

    public function testIdiormLastStatement()
    {
        IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

        $eventModel = new IdiormDbal('events');

        $eventModel->criteria('country', '=', 'Ireland')->get();

        $this->assertEquals("SELECT * FROM `events` WHERE `country` = ?", $eventModel::lastStatement()->queryString);
    }

    public function testIdiormQueryLog()
    {
        $eventModel = new IdiormDbal('events');

        $eventModel->criteria('country', '=', 'Ireland')->get();

        $userModel = new IdiormDbal('users');

        $userModel->get();

        $this->assertIsArray($eventModel::queryLog());

        $this->assertGreaterThan(0, count($eventModel::queryLog()));
    }
}
