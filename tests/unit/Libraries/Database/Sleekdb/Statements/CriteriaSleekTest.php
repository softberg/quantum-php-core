<?php

namespace Quantum\Tests\Libraries\Database\Sleekdb\Statements;

use Quantum\Tests\Libraries\Database\Sleekdb\SleekDbalTestCase;
use Quantum\Libraries\Database\Sleekdb\SleekDbal;

class CriteriaSleekTest extends SleekDbalTestCase
{

    public function testSleekCriteriaEquals()
    {
        $this->userModel = new SleekDbal('users');

        $user = $this->userModel->criteria('firstname', '=', 'John')->first();

        $this->assertEquals('John', $user->prop('firstname'));

        $this->assertEquals('Doe', $user->prop('lastname'));
    }

    public function testSleekCriteriaNotEquals()
    {
        $this->userModel = new SleekDbal('users');

        $user = $this->userModel->criteria('firstname', '!=', 'John')->first();

        $this->assertEquals('Jane', $user->prop('firstname'));

        $this->assertEquals('Du', $user->prop('lastname'));
    }

    public function testSleekCriteriaGreaterAndGreaterOrEqual()
    {
        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->criteria('age', '>', 35)->get();

        $this->assertCount(1, $users);

        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->criteria('age', '>=', 35)->get();

        $this->assertCount(2, $users);
    }

    public function testSleekCriteriaSmallerAndSmallerOrEqual()
    {
        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->criteria('age', '<', 35)->get();

        $this->assertCount(0, $users);

        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->criteria('age', '<=', 35)->get();

        $this->assertCount(1, $users);
    }

    public function testSleekCriteriaInAndNotIn()
    {
        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->criteria('age', 'IN', [35, 40, 45])->orderBy('age', 'desc')->get();

        $this->assertCount(2, $users);

        $this->assertEquals('John', $users[0]['firstname']);

        $this->assertEquals('Jane', $users[1]['firstname']);

        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->criteria('age', 'NOT IN', [30, 40, 45])->get();

        $this->assertCount(1, $users);

        $this->assertEquals('Jane', $users[0]['firstname']);
    }

    public function testSleekCriteriaLikeAndNotLike()
    {
        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->criteria('firstname', 'LIKE', '%Jo%')->get();

        $this->assertCount(1, $users);

        $this->assertEquals('John', $users[0]['firstname']);

        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->criteria('firstname', 'LIKE', '%J%')->orderBy('firstname', 'desc')->get();

        $this->assertCount(2, $users);

        $this->assertEquals('Jane', $users[1]['firstname']);

        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->criteria('firstname', 'NOT LIKE', '%Jo%')->get();

        $this->assertCount(1, $users);

        $this->assertEquals('Jane', $users[0]['firstname']);
    }

    public function testSleekCriteriaBetweenAndNotBetween()
    {
        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->criteria('age', 'BETWEEN', [20, 40])->get();

        $this->assertCount(1, $users);

        $this->assertEquals('Jane', $users[0]['firstname']);

        $this->userModel = new SleekDbal('users');

        $users = $this->userModel->criteria('age', 'NOT BETWEEN', [0, 40])->get();

        $this->assertCount(1, $users);

        $this->assertEquals('John', $users[0]['firstname']);
    }

    public function testSleekMultipleAndCriterias()
    {
        $eventsModel = new SleekDbal('events');

        $events = $eventsModel->criterias(
            ['title', '=', 'Music'],
            ['started_at', '>=', new \DateTime("2020-12-01")]
        )->get();

        $this->assertIsArray($events);

        $this->assertEquals('Music', $events[0]['title']);

        $this->assertGreaterThan('2020-12-01', $events[0]['started_at']);

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

}