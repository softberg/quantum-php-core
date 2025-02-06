<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Sleekdb\Statements;

use Quantum\Tests\Unit\Libraries\Database\Adapters\Sleekdb\SleekDbalTestCase;
use Quantum\Libraries\Database\Adapters\Sleekdb\SleekDbal;

class ReducerSleekTest extends SleekDbalTestCase
{

    public function testSleekSelect()
    {
        $userModel = new SleekDbal('users');

        $user = $userModel->select('age')->first();

        $this->assertCount(2, $user->asArray());

        $userModel = new SleekDbal('users');

        $user = $userModel
            ->select('id', ['firstname' => 'name'], ['lastname' => 'surname'], 'age')
            ->orderBy('id', 'asc')
            ->get();

        $this->assertEquals('John', $user[0]->name);

        $this->assertEquals('Doe', $user[0]->surname);
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

        $events = $eventsModel->orderBy('title', 'desc')->get();

        $this->assertEquals('Music', $events[0]->title);

        $this->assertEquals('Art', $events[count($events) - 1]->title);
    }

    public function testSleekLimitAndOffset()
    {
        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->limit(3)->orderBy('id', 'asc')->get();

        $this->assertCount(3, $events);

        $this->assertEquals(1, $events[0]->id);

        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->orderBy('id', 'asc')->offset(1)->limit(3)->get();

        $this->assertCount(3, $events);

        $this->assertEquals(2, $events[0]->id);
    }
}