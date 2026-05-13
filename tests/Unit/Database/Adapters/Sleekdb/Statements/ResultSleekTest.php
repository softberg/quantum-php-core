<?php

namespace Quantum\Tests\Unit\Database\Adapters\Sleekdb\Statements;

use Quantum\Tests\Unit\Database\Adapters\Sleekdb\SleekDbalTestCase;
use Quantum\Tests\_root\shared\Models\TestEventModel;
use Quantum\Database\Adapters\Sleekdb\SleekDbal;

class ResultSleekTest extends SleekDbalTestCase
{
    private SleekDbal $userProfileModel;

    public function setUp(): void
    {
        parent::setUp();

        $this->userProfileModel = new SleekDbal('profiles');
    }

    public function testSleekGet(): void
    {
        $users = $this->userProfileModel->get();

        $this->assertIsArray($users);

        $this->assertInstanceOf(SleekDbal::class, $users[0]);

        $this->assertNotNull($users[0]->firstname);

        $this->assertNotNull($users[0]->lastname);
    }

    public function testSleekFindOne(): void
    {
        $user = $this->userProfileModel->findOne(1);

        $this->assertEquals('John', $user->prop('firstname'));

        $this->assertEquals('Doe', $user->prop('lastname'));
    }

    public function testSleekFindOneBy(): void
    {
        $user = $this->userProfileModel->findOneBy('firstname', 'John');

        $this->assertEquals('Doe', $user->prop('lastname'));

        $this->assertEquals('45', $user->prop('age'));
    }

    public function testSleekFirst(): void
    {
        $user = $this->userProfileModel->criteria('age', '>', 40)->first();

        $this->assertEquals('Doe', $user->prop('lastname'));

        $this->assertEquals('45', $user->prop('age'));

        $userModel = new SleekDbal('profiles');

        $user = $userModel->criteria('age', '<', 40)->first();

        $this->assertEquals('Jane', $user->prop('firstname'));

        $this->assertEquals('Du', $user->prop('lastname'));

        $this->assertEquals('35', $user->prop('age'));
    }

    public function testSleekCount(): void
    {
        $this->assertIsInt($this->userProfileModel->count());

        $this->assertEquals(2, $this->userProfileModel->count());

        $this->userProfileModel->criteria('age', '>', 40);

        $this->assertEquals(1, $this->userProfileModel->count());
    }

    public function testSleekAsArray(): void
    {
        $user = $this->userProfileModel->first();

        $this->assertIsObject($user);

        $this->assertIsArray($user->asArray());
    }

    public function testSleekCountResetsCriteriaState(): void
    {
        $eventsModel = new SleekDbal('events');

        $eventsModel->criteria('country', '=', 'Ireland');

        $this->assertEquals(3, $eventsModel->count());

        $events = $eventsModel
            ->orderBy('title', 'asc')
            ->get();

        $this->assertCount(7, $events);
        $this->assertEquals('Art', $events[0]->prop('title'));
    }

    public function testSleekPaginateRetainsCriteriaAfterCount(): void
    {
        $eventModel = model(TestEventModel::class);

        $page = $eventModel
            ->criteria('country', '=', 'Ireland')
            ->orderBy('title', 'asc')
            ->paginate(2, 1)
            ->data();

        $this->assertCount(2, $page);
        $this->assertEquals('Design', $page->first()->title);
        $this->assertEquals('Film', $page->last()->title);
    }
}
