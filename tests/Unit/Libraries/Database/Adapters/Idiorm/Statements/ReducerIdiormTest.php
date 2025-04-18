<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\IdiormDbalTestCase;
use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;

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

        $this->assertEquals('Art', $events[0]->prop('title'));

        $this->assertEquals('Music', $events[count($events) - 1]->prop('title'));
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

        $this->assertEquals(1, $events[0]->prop('id'));

        $eventsModel = new IdiormDbal('events');

        $events = $eventsModel->offset(1)->limit(3)->get();

        $this->assertCount(3, $events);

        $this->assertEquals(2, $events[0]->prop('id'));
    }
}
