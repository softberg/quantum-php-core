<?php

namespace Quantum\Test\Unit;

use Quantum\Libraries\Database\Sleekdb\SleekDbal;

require_once dirname(__DIR__) . DS . 'SleekDbalTestCase.php';

class ReducerSleekTest extends SleekDbalTestCase
{

    public function testSleekSelect()
    {
        $userModel = new SleekDbal('users');

        $user = $userModel->select('age')->first();

        $this->assertCount(2, $user->asArray());

        $userModel = new SleekDbal('users');

        $user = $userModel->select('id', ['firstname' => 'name'], ['lastname' => 'surname'])->first();

        $this->assertEquals('John', $user->prop('name'));

        $this->assertEquals('Doe', $user->prop('surname'));
    }

    public function testSleekGroupBy()
    {
        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->groupBy('country')->get();

        $this->assertCount(4, $events);

        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->groupBy('title')->get();

        $this->assertCount(5, $events);
    }

    public function testSleekOrderBy()
    {
        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->orderBy('title', 'asc')->get();

        $this->assertEquals('Art', $events[0]['title']);

        $this->assertEquals('Music', $events[count($events) - 1]['title']);
    }

    public function testSleekLimitAndOffset()
    {
        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->limit(3)->get();

        $this->assertCount(3, $events);

        $this->assertEquals(1, $events[0]['id']);

        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->offset(1)->limit(3)->get();

        $this->assertCount(3, $events);

        $this->assertEquals(2, $events[0]['id']);
    }

}