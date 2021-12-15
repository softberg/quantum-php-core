<?php

namespace Quantum\Test\Unit;

use Quantum\Libraries\Database\Idiorm\IdiormDbal;

require_once dirname(__DIR__) . DS . 'IdiormDbalTestCase.php';

class ReducerIdiormTest extends IdiormDbalTestCase
{

    public function testIdiormSelect()
    {
        $userModel = new IdiormDbal('users');

        $user = $userModel->select('id', 'age')->first();

        $this->assertCount(2, $user->asArray());

        $userModel = new IdiormDbal('users');

        $user = $userModel->select('id', ['firstname' => 'name'], ['lastname' => 'surname'])->first();

        $this->assertEquals('John', $user->prop('name'));

        $this->assertEquals('Doe', $user->prop('surname'));
    }

    public function testIdiormOrderBy()
    {
        $eventsModel = new IdiormDbal('events');

        $events = $eventsModel->orderBy('title', 'asc')->get();

        $this->assertEquals('Art', $events[0]['title']);

        $this->assertEquals('Music', $events[count($events) - 1]['title']);
    }

    public function testIdiormGroupBy()
    {
        $eventsModel = new IdiormDbal('events');

        $events = $eventsModel->groupBy('country')->get();

        $this->assertCount(4, $events);

        $eventsModel = new IdiormDbal('events');

        $events = $eventsModel->groupBy('title')->get();

        $this->assertCount(5, $events);
    }

    public function testIdiormLimitAndOffset()
    {
        $eventsModel = new IdiormDbal('events');

        $events = $eventsModel->limit(3)->get();

        $this->assertCount(3, $events);

        $this->assertEquals(1, $events[0]['id']);

        $eventsModel = new IdiormDbal('events');

        $events = $eventsModel->offset(1)->limit(3)->get();

        $this->assertCount(3, $events);

        $this->assertEquals(2, $events[0]['id']);
    }

}
