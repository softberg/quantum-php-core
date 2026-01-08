<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Sleekdb\Statements;

use Quantum\Tests\Unit\Libraries\Database\Adapters\Sleekdb\SleekDbalTestCase;
use Quantum\Libraries\Database\Adapters\Sleekdb\SleekDbal;

class CriteriaSleekTest extends SleekDbalTestCase
{

    public $userProfileModel;
    public function testSleekCriteriaEquals()
    {
        $this->userProfileModel = new SleekDbal('profiles');

        $user = $this->userProfileModel->criteria('firstname', '=', 'John')->first();

        $this->assertEquals('John', $user->prop('firstname'));

        $this->assertEquals('Doe', $user->prop('lastname'));
    }

    public function testSleekCriteriaNotEquals()
    {
        $this->userProfileModel = new SleekDbal('profiles');

        $user = $this->userProfileModel->criteria('firstname', '!=', 'John')->first();

        $this->assertEquals('Jane', $user->prop('firstname'));

        $this->assertEquals('Du', $user->prop('lastname'));
    }

    public function testSleekCriteriaGreaterAndGreaterOrEqual()
    {
        $this->userProfileModel = new SleekDbal('profiles');

        $users = $this->userProfileModel->criteria('age', '>', 35)->get();

        $this->assertCount(1, $users);

        $this->userProfileModel = new SleekDbal('profiles');

        $users = $this->userProfileModel->criteria('age', '>=', 35)->get();

        $this->assertCount(2, $users);
    }

    public function testSleekCriteriaSmallerAndSmallerOrEqual()
    {
        $this->userProfileModel = new SleekDbal('profiles');

        $users = $this->userProfileModel->criteria('age', '<', 35)->get();

        $this->assertCount(0, $users);

        $this->userProfileModel = new SleekDbal('profiles');

        $users = $this->userProfileModel->criteria('age', '<=', 35)->get();

        $this->assertCount(1, $users);
    }

    public function testSleekCriteriaInAndNotIn()
    {
        $this->userProfileModel = new SleekDbal('profiles');

        $users = $this->userProfileModel->criteria('age', 'IN', [35, 40, 45])->orderBy('age', 'desc')->get();

        $this->assertCount(2, $users);

        $this->assertEquals('John', $users[0]->firstname);

        $this->assertEquals('Jane', $users[1]->firstname);

        $this->userProfileModel = new SleekDbal('profiles');

        $users = $this->userProfileModel->criteria('age', 'NOT IN', [30, 40, 45])->get();

        $this->assertCount(1, $users);

        $this->assertEquals('Jane', $users[0]->firstname);
    }

    public function testSleekCriteriaLikeAndNotLike()
    {
        $this->userProfileModel = new SleekDbal('profiles');

        $users = $this->userProfileModel->criteria('firstname', 'LIKE', '%Jo%')->get();

        $this->assertCount(1, $users);

        $this->assertEquals('John', $users[0]->firstname);

        $this->userProfileModel = new SleekDbal('profiles');

        $users = $this->userProfileModel->criteria('firstname', 'LIKE', '%J%')->orderBy('firstname', 'desc')->get();

        $this->assertCount(2, $users);

        $this->assertEquals('Jane', $users[1]->firstname);

        $this->userProfileModel = new SleekDbal('profiles');

        $users = $this->userProfileModel->criteria('firstname', 'NOT LIKE', '%Jo%')->get();

        $this->assertCount(1, $users);

        $this->assertEquals('Jane', $users[0]->firstname);
    }

    public function testSleekCriteriaBetweenAndNotBetween()
    {
        $this->userProfileModel = new SleekDbal('profiles');

        $users = $this->userProfileModel->criteria('age', 'BETWEEN', [20, 40])->get();

        $this->assertCount(1, $users);

        $this->assertEquals('Jane', $users[0]->firstname);

        $this->userProfileModel = new SleekDbal('profiles');

        $users = $this->userProfileModel->criteria('age', 'NOT BETWEEN', [0, 40])->get();

        $this->assertCount(1, $users);

        $this->assertEquals('John', $users[0]->firstname);
    }

    public function testSleekMultipleAndCriterias()
    {
        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->criterias(
            ['title', '=', 'Music'],
            ['started_at', '>=', new \DateTime("2020-12-01")]
        )->get();

        $this->assertIsArray($events);

        $this->assertEquals('Music', $events[0]->title);

        $this->assertGreaterThan('2020-12-01', $events[0]->started_at);

    }

    public function testSleekMultipleOrCriterias()
    {
        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->criterias([
            ['title', '=', 'Music'],
            ['title', '=', 'Dance'],
            ['title', '=', 'Art']
        ])->get();

        $this->assertIsArray($events);

        $this->assertCount(5, $events);
    }

    public function testSleekAndOrCriterias()
    {
        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->criterias([
            ['title', '=', 'Dance'],
            ['title', '=', 'Art']
        ], ['country', '=', 'Ireland'])->get();

        $this->assertIsArray($events);

        $this->assertCount(1, $events);
    }

    public function testSleekHaving()
    {
        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->criteria('title', '=', 'Music')
            ->having('country', '=', 'Ireland')
            ->get();

        $this->assertIsArray($events);

        $this->assertCount(1, $events);
    }

    public function testSleekIsNull()
    {
        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->isNull('started_at')->orderBy('id', 'asc')->get();

        $this->assertCount(2, $events);

        $this->assertEquals('Art', $events[0]->prop('title'));

        $this->assertEquals('Music', $events[1]->prop('title'));
    }

    public function testSleekIsNotNull()
    {
        $eventsModel = new SleekDbal('events');

        $eventsModel->isNotNull('started_at');

        $events = $eventsModel->get();

        $this->assertGreaterThan(0, count($events));

        foreach ($events as $event) {
            $this->assertNotNull($event->prop('started_at'));
        }
    }
}