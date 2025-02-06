<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\IdiormDbalTestCase;
use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;

class ModelIdiormTest extends IdiormDbalTestCase
{

    public function testIdiormCreateNewRecord()
    {
        $eventsModel = new IdiormDbal('events');

        $this->assertEquals(7, $eventsModel->count());

        $event = $eventsModel->create();

        $event->prop('title', 'Biking');

        $event->prop('country', 'New Zealand');

        $event->prop('started_at', '2020-07-11 11:00:00');

        $event->save();

        $this->assertEquals(8, $eventsModel->count());

        $bikingEvent = $eventsModel->criteria('title', '=', 'Biking')->first();

        $this->assertEquals('Biking', $bikingEvent->prop('title'));

        $this->assertEquals('New Zealand', $bikingEvent->prop('country'));

        $this->assertEquals('2020-07-11 11:00:00', $bikingEvent->prop('started_at'));

    }

    public function testIdiormUpdateExistingRecord()
    {
        $eventsModel = new IdiormDbal('events');

        $event = $eventsModel->findOne(1);

        $this->assertEquals('Dance', $event->prop('title'));

        $event->prop('title', 'Climbing');

        $event->save();

        $eventsModel = new IdiormDbal('events');

        $event = $eventsModel->findOne(1);

        $this->assertEquals('Climbing', $event->prop('title'));
    }

    public function testIdiormDeleteRecord()
    {
        $eventModel = new IdiormDbal('events');

        $this->assertEquals(7, $eventModel->count());

        $event = $eventModel->findOne(1);

        $event->delete();

        $eventModel = new IdiormDbal('events');

        $this->assertEquals(6, $eventModel->count());

        $event = $eventModel->findOne(1);

        $this->assertNull($event->prop('title'));
    }

    public function testIdiormDeleteMany()
    {
        $eventsModel = new IdiormDbal('events');

        $this->assertEquals(7, $eventsModel->count());

        $eventsModel->criteria('title', '=', 'Dance')->deleteMany();

        $eventsModel = new IdiormDbal('events');

        $this->assertCount(6, $eventsModel->get());

        $events = $eventsModel->criteria('title', '=', 'Dance')->get();

        $this->assertEmpty($events);
    }
}
