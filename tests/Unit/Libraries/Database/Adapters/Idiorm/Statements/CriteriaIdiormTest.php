<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\IdiormDbalTestCase;
use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;

class CriteriaIdiormTest extends IdiormDbalTestCase
{

    public function testIdiormCriteriaEquals()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $user = $userProfileModel->criteria('firstname', '=', 'John')->first();

        $this->assertEquals('John', $user->prop('firstname'));

        $this->assertEquals('Doe', $user->prop('lastname'));
    }

    public function testIdiormCriteriaNotEquals()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $user = $userProfileModel->criteria('firstname', '!=', 'John')->first();

        $this->assertEquals('Jane', $user->prop('firstname'));
    }

    public function testIdiormCriteriaGreaterAndGreaterOrEqual()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->criteria('age', '>', 35)->get();

        $this->assertCount(1, $users);

        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->criteria('age', '>=', 35)->get();

        $this->assertCount(2, $users);
    }

    public function testIdiormCriteriaSmallerAndSmallerOrEqual()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->criteria('age', '<', 35)->get();

        $this->assertCount(0, $users);

        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->criteria('age', '<=', 35)->get();

        $this->assertCount(1, $users);
    }

    public function testIdiormCriteriaInAndNotIn()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->criteria('age', 'IN', [35, 40, 45])->get();

        $this->assertCount(2, $users);

        $this->assertEquals('John', $users[0]->prop('firstname'));

        $this->assertEquals('Jane', $users[1]->prop('firstname'));

        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->criteria('age', 'NOT IN', [30, 40, 45])->get();

        $this->assertCount(1, $users);

        $this->assertEquals('Jane', $users[0]->prop('firstname'));
    }

    public function testIdiormCriteriaLikeAndNotLike()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->criteria('firstname', 'LIKE', '%Jo%')->get();

        $this->assertCount(1, $users);

        $this->assertEquals('John', $users[0]->prop('firstname'));

        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->criteria('firstname', 'LIKE', '%J%')->get();

        $this->assertCount(2, $users);

        $this->assertEquals('Jane', $users[1]->prop('firstname'));

        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->criteria('firstname', 'NOT LIKE', '%Jo%')->get();

        $this->assertCount(1, $users);

        $this->assertEquals('Jane', $users[0]->prop('firstname'));
    }

    public function testIdiormCriteriaNullAndNotNull()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->criteria('firstname', 'NULL', '')->get();

        $this->assertCount(0, $users);

        $userProfileModel = new IdiormDbal('profiles');

        $users = $userProfileModel->criteria('firstname', 'NOT NULL', '')->get();

        $this->assertCount(2, $users);

        $this->assertEquals('John', $users[0]->prop('firstname'));

        $this->assertEquals('Jane', $users[1]->prop('firstname'));
    }

    public function testIdiormCriteriaWithFunction()
    {
        $now = '2035-01-01 00:00:00';

        $eventModel = new IdiormDbal('events');

        $events = $eventModel->criteria('started_at', '>=', ['fn' => "'$now'"])->get();

        $this->assertCount(1, $events);

        $this->assertEquals('2040-02-14 10:15:12', $events[0]->prop('started_at'));
    }

    public function testIdiormCriteriaColumnsEqual()
    {
        $userProfileModel = new IdiormDbal('profiles');

        $userProfileModel->join('user_events', ['user_events.user_id', '=', 'profiles.user_id'])
            ->join('events', ['user_events.event_id', '=', 'events.id'])
            ->criteria('profiles.country', '#=#', 'events.country')
            ->get();

        $expectedQuery = "SELECT * FROM `profiles` JOIN `user_events` ON `user_events`.`user_id` = `profiles`.`user_id` JOIN `events` ON `user_events`.`event_id` = `events`.`id` WHERE profiles.country = events.country";

        $this->assertEquals($expectedQuery, IdiormDbal::lastQuery());
    }

    public function testIdiormMultipleAndCriterias()
    {
        $eventsModel = new IdiormDbal('events');

        $eventsModel->criterias(
            ['title', '=', 'Music'],
            ['country', '=', 'Island'],
            ['started_at', '>=', ['fn' => 'date("now")']]
        )->get();

        $expectedQuery = "SELECT * FROM `events` WHERE `title` = 'Music' AND `country` = 'Island' AND started_at >= date(\"now\")";

        $this->assertEquals($expectedQuery, IdiormDbal::lastQuery());
    }

    public function testIdiormMultipleOrCriterias()
    {
        $eventsModel = new IdiormDbal('events');

        $eventsModel->criterias([
            ['title', '=', 'Music'],
            ['title', '=', 'Dance'],
            ['title', '=', 'Art']
        ])->groupBy('title')->get();

        $expectedQuery = "SELECT * FROM `events` WHERE (`title` = 'Music' OR `title` = 'Dance' OR `title` = 'Art') GROUP BY `title`";

        $this->assertEquals($expectedQuery, IdiormDbal::lastQuery());
    }

    public function testIdiormAndOrCriterias()
    {
        $eventsModel = new IdiormDbal('events');

        $eventsModel->criterias([
            ['title', '=', 'Music'],
            ['title', '=', 'Design']
        ], ['country', '=', 'Ireland'])->get();

        $expectedQuery = "SELECT * FROM `events` WHERE (`title` = 'Music' OR `title` = 'Design') AND `country` = 'Ireland'";

        $this->assertEquals($expectedQuery, IdiormDbal::lastQuery());
    }

    public function testIdiormHaving()
    {
        $eventsModel = new IdiormDbal('events');

        $events = $eventsModel->criteria('title', '=', 'Music')
            ->groupBy('country')
            ->having('country', '=', 'Ireland')
            ->get();

        $this->assertIsArray($events);

        $this->assertCount(1, $events);

        $this->assertEquals('Music', $events[0]->prop('title'));

        $this->assertEquals('Ireland', $events[0]->prop('country'));
    }

    public function testIdiormIsNull()
    {
        $eventsModel = new IdiormDbal('events');

        $events = $eventsModel->isNull('started_at')->get();

        $this->assertCount(2, $events);

        $this->assertEquals('Art', $events[0]->prop('title'));

        $this->assertEquals('Music', $events[1]->prop('title'));
    }

    public function testIdiormIsNotNull()
    {
        $eventsModel = new IdiormDbal('events');

        $eventsModel->isNotNull('started_at');

        $events = $eventsModel->get();

        $this->assertGreaterThan(0, count($events));

        foreach ($events as $event) {
            $this->assertNotNull($event->prop('started_at'));
        }
    }
}