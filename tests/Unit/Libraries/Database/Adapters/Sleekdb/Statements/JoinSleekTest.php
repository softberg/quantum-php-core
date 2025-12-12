<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Sleekdb\Statements;

use Quantum\Tests\Unit\Libraries\Database\Adapters\Sleekdb\SleekDbalTestCase;
use Quantum\Tests\_root\shared\Models\TestUserProfessionModel;
use Quantum\Tests\_root\shared\Models\TestUserMeetingModel;
use Quantum\Tests\_root\shared\Models\TestUserEventModel;
use Quantum\Tests\_root\shared\Models\TestTicketModel;
use Quantum\Tests\_root\shared\Models\TestEventModel;
use Quantum\Tests\_root\shared\Models\TestNotesModel;
use Quantum\Tests\_root\shared\Models\TestUserModel;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Model\ModelCollection;


class JoinSleekTest extends SleekDbalTestCase
{

    public function testSleekSameLevelJoinTo()
    {
        $userModel = ModelFactory::get(TestUserModel::class);

        $userProfessionModel = ModelFactory::get(TestUserProfessionModel::class);

        $userMeetings = ModelFactory::get(TestUserMeetingModel::class);

        $users = $userModel
            ->joinTo($userProfessionModel, false)
            ->joinTo($userMeetings)
            ->get();

        $this->assertInstanceOf(ModelCollection::class, $users);

        $this->assertIsArray($users->first()->prop('user_professions'));

        $this->assertIsArray($users->first()->prop('user_meetings'));
    }

    public function testSleekNestedLevelJoinTo()
    {
        $userModel = ModelFactory::get(TestUserModel::class);

        $meetingModel = ModelFactory::get(TestUserMeetingModel::class);

        $ticketModel = ModelFactory::get(TestTicketModel::class);

        $noteModel = ModelFactory::get(TestNotesModel::class);

        $users = $userModel
            ->joinTo($meetingModel)
            ->joinTo($ticketModel)
            ->joinTo($noteModel)
            ->get();

        $this->assertInstanceOf(ModelCollection::class, $users);

        $this->assertCount(2, $users);

        $this->assertIsArray($users->first()->prop('user_meetings')[0]);

        $this->assertArrayHasKey('tickets', $users->first()->prop('user_meetings')[0]);

        $this->assertIsArray($users->first()->prop('user_meetings')[0]['tickets'][0]);

        $this->assertArrayHasKey('notes', $users->first()->prop('user_meetings')[0]['tickets'][0]);
    }

    public function testSleekMixedLevelJoinToWithCriteria()
    {
        $userModel = ModelFactory::get(TestUserModel::class);

        $userProfessionModel = ModelFactory::get(TestUserProfessionModel::class);

        $meetingModel = ModelFactory::get(TestUserMeetingModel::class);

        $ticketModel = ModelFactory::get(TestTicketModel::class);

        $users = $userModel
            ->joinTo($userProfessionModel, false)
            ->joinTo($meetingModel)
            ->joinTo($ticketModel)
            ->criteria('age', '=', 35)
            ->get();

        $this->assertInstanceOf(ModelCollection::class, $users);

        $this->assertCount(1, $users);

        $this->assertEquals('Jane', $users->first()->prop('firstname'));

        $this->assertIsArray($users->first()->prop('user_professions'));

        $this->assertIsArray($users->first()->prop('user_meetings'));

        $this->assertEquals('Marketing', $users->first()->prop('user_meetings')[0]['title']);

        $this->assertArrayHasKey('tickets', $users->first()->prop('user_meetings')[0]);

        $this->assertIsArray($users->first()->prop('user_meetings')[0]['tickets']);
    }

    public function testSleekJoinToAndThrough()
    {
        $userModel = ModelFactory::get(TestUserModel::class);

        $userEventModel = ModelFactory::get(TestUserEventModel::class);

        $eventModel = ModelFactory::get(TestEventModel::class);

        $users = $userModel
            ->joinTo($userEventModel)
            ->joinThrough($eventModel)
            ->orderBy('title', 'asc')
            ->get();

        $this->assertInstanceOf(ModelCollection::class, $users);

        $this->assertIsArray($users->first()->user_events);

        $this->assertArrayHasKey('confirmed', $users->first()->user_events[0]);

        $this->assertArrayHasKey('events', $users->first()->user_events[0]);

        $this->assertIsArray($users->first()->user_events[0]['events']);

        $this->assertArrayHasKey('title', $users->first()->user_events[0]['events'][0]);
    }

    public function testSleekJoinThroughInverse()
    {
        $ticketModel = ModelFactory::get(TestTicketModel::class);

        $noteModel = ModelFactory::get(TestNotesModel::class);

        $notes = $noteModel->joinThrough($ticketModel)->criteria('id', '=', 3)->first()->asArray();

        $this->assertArrayHasKey('note', $notes);

        $this->assertEquals('note three', $notes['note']);

        $this->assertArrayHasKey('tickets', $notes);

        $this->assertIsArray($notes['tickets']);

        $this->assertArrayHasKey('number', $notes['tickets'][0]);

        $this->assertEquals('R4563', $notes['tickets'][0]['number']);
    }

    public function testSleekJoinToAndJoinThroughUsingSwitch()
    {
        $userModel = ModelFactory::get(TestUserModel::class);

        $userProfessionModel = ModelFactory::get(TestUserProfessionModel::class);

        $userEventModel = ModelFactory::get(TestUserEventModel::class);

        $eventModel = ModelFactory::get(TestEventModel::class);

            $users = $userModel
                ->joinTo($userProfessionModel, false)
                ->joinTo($userEventModel)
                ->joinThrough($eventModel)
                ->orderBy('title', 'asc')
                ->get();

        $this->assertInstanceOf(ModelCollection::class, $users);

        $this->assertIsArray($users->first()->user_professions);

        $this->assertArrayHasKey('title', $users->first()->user_professions[0]);

        $this->assertEquals('Singer', $users->first()->user_professions[0]['title']);
    }

    public function testSleekWrongRelation()
    {
        $this->expectException(ModelException::class);

        $this->expectExceptionMessage('The model `' . TestNotesModel::class . '` does not define relation with `' . TestEventModel::class . '`');

        $eventModel = ModelFactory::get(TestEventModel::class);

        $noteModel = ModelFactory::get(TestNotesModel::class);

        $eventModel->joinTo($noteModel)->get();
    }

    public function testSleekSelectFieldsAtJoin()
    {
        $userModel = ModelFactory::get(TestUserModel::class);

        $userProfessionModel = ModelFactory::get(TestUserProfessionModel::class);

        $users = $userModel->joinTo($userProfessionModel)
            ->select('firstname', 'lastname', 'age', 'country', ['user_professions.title' => 'profession'])
            ->orderBy('age', 'desc')
            ->get();

        $this->assertEquals('Writer', $users->first()->profession);
    }
}