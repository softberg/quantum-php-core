<?php

namespace Quantum\Models {

    use Quantum\Mvc\QtModel;

    class SleekUserModel extends QtModel
    {

        public $table = 'users';

    }

    class SleekUserProfessionModel extends QtModel
    {

        public $table = 'professions';
        public $foreignKeys = [
            'users' => 'user_id'
        ];

    }

    class SleekUserEventModel extends QtModel
    {

        public $table = 'user_events';
        public $foreignKeys = [
            'users' => 'user_id',
            'events' => 'event_id'
        ];

    }

    class SleekEventModel extends QtModel
    {

        public $table = 'events';
        public $foreignKeys = [
            'user_events' => 'event_id'
        ];

    }

    class SleekMeetingModel extends QtModel
    {

        public $table = 'meetings';
        public $foreignKeys = [
            'users' => 'user_id'
        ];

    }

    class SleekTicketModel extends QtModel
    {

        public $table = 'tickets';
        public $foreignKeys = [
            'meetings' => 'meeting_id'
        ];

    }

    class SleekNotesModel extends QtModel
    {

        public $table = 'notes';
        public $foreignKeys = [
            'tickets' => 'ticket_id'
        ];

    }

}

namespace Quantum\Tests\Libraries\Database\Sleekdb\Statements {

    use Quantum\Factory\ModelFactory;
    use Quantum\Libraries\Database\Exceptions\ModelException;
    use Quantum\Models\SleekEventModel;
    use Quantum\Models\SleekMeetingModel;
    use Quantum\Models\SleekNotesModel;
    use Quantum\Models\SleekTicketModel;
    use Quantum\Models\SleekUserEventModel;
    use Quantum\Models\SleekUserModel;
    use Quantum\Models\SleekUserProfessionModel;
    use Quantum\Tests\Libraries\Database\Sleekdb\SleekDbalTestCase;

    /**
     * @runTestsInSeparateProcesses
     */
    class JoinSleekTest extends SleekDbalTestCase
    {

        public function testSleekSameLevelJoinTo()
        {
            $userModel = ModelFactory::get(SleekUserModel::class);

            $userProfessionModel = ModelFactory::get(SleekUserProfessionModel::class);

            $userMeetings = ModelFactory::get(SleekMeetingModel::class);

            $users = $userModel
                ->joinTo($userProfessionModel, false)
                ->joinTo($userMeetings)
                ->get();

            $this->assertIsArray($users);
            
            $this->assertIsArray($users[0]->professions);

            $this->assertIsArray($users[0]->meetings);
        }

        public function testSleekNestedLevelJoinTo()
        {
            $userModel = ModelFactory::get(SleekUserModel::class);

            $meetingModel = ModelFactory::get(SleekMeetingModel::class);

            $ticketModel = ModelFactory::get(SleekTicketModel::class);

            $noteModel = ModelFactory::get(SleekNotesModel::class);

            $users = $userModel
                ->joinTo($meetingModel)
                ->joinTo($ticketModel)
                ->joinTo($noteModel)
                ->get();

            $this->assertIsArray($users);

            $this->assertCount(2, $users);

            $this->assertArrayHasKey('tickets', $users[0]->meetings[0]);

            $this->assertArrayHasKey('notes', $users[0]->meetings[0]['tickets'][0]);
        }

        public function testSleekMixedLevelJoinToWithCriteria()
        {
            $userModel = ModelFactory::get(SleekUserModel::class);

            $userProfessionModel = ModelFactory::get(SleekUserProfessionModel::class);

            $meetingModel = ModelFactory::get(SleekMeetingModel::class);

            $ticketModel = ModelFactory::get(SleekTicketModel::class);

            $users = $userModel
                ->joinTo($userProfessionModel, false)
                ->joinTo($meetingModel)
                ->joinTo($ticketModel)
                ->criteria('age', '=', 35)
                ->get();

            $this->assertIsArray($users);

            $this->assertCount(1, $users);

            $this->assertEquals('Jane', $users[0]->firstname);

            $this->assertIsArray($users[0]->professions);

            $this->assertIsArray($users[0]->meetings[0]);

            $this->assertEquals('Marketing', $users[0]->meetings[0]['title']);

            $this->assertArrayHasKey('tickets', $users[0]->meetings[0]);

            $this->assertIsArray($users[0]->meetings[0]['tickets']);
        }

        public function testSleekJoinToAndThrough()
        {
            $userModel = ModelFactory::get(SleekUserModel::class);

            $userEventModel = ModelFactory::get(SleekUserEventModel::class);

            $eventModel = ModelFactory::get(SleekEventModel::class);

            $users = $userModel
                ->joinTo($userEventModel)
                ->joinThrough($eventModel)
                ->orderBy('title', 'asc')
                ->get();

            $this->assertIsArray($users);

            $this->assertIsArray($users[0]->user_events);

            $this->assertArrayHasKey('confirmed', $users[0]->user_events[0]);

            $this->assertArrayHasKey('events', $users[0]->user_events[0]);

            $this->assertIsArray($users[0]->user_events[0]['events']);

            $this->assertArrayHasKey('title', $users[0]->user_events[0]['events'][0]);
        }

        public function testSleekJoinThroughInverse()
        {
            $ticketModel = ModelFactory::get(SleekTicketModel::class);

            $noteModel = ModelFactory::get(SleekNotesModel::class);

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
            $userModel = ModelFactory::get(SleekUserModel::class);

            $userProfessionModel = ModelFactory::get(SleekUserProfessionModel::class);

            $userEventModel = ModelFactory::get(SleekUserEventModel::class);

            $eventModel = ModelFactory::get(SleekEventModel::class);

            $users = $userModel
                ->joinTo($userProfessionModel, false)
                ->joinTo($userEventModel)
                ->joinThrough($eventModel)
                ->orderBy('title', 'asc')
                ->get();

            $this->assertIsArray($users);

            $this->assertIsArray($users[0]->professions);

            $this->assertArrayHasKey('title', $users[0]->professions[0]);

            $this->assertEquals('Singer', $users[0]->professions[0]['title']);
        }

        public function testSleekWrongRelation()
        {
            $this->expectException(ModelException::class);

            $this->expectExceptionMessage('wrong_relation');

            $eventModel = ModelFactory::get(SleekEventModel::class);

            $noteModel = ModelFactory::get(SleekNotesModel::class);

            $eventModel->joinTo($noteModel)->get();
        }

        public function testSleekSelectFieldsAtJoin()
        {
            $userModel = ModelFactory::get(SleekUserModel::class);

            $userProfessionModel = ModelFactory::get(SleekUserProfessionModel::class);

            $users = $userModel->joinTo($userProfessionModel)
                ->select('firstname', 'lastname', 'age', 'country', ['professions.title' => 'profession'])
                ->orderBy('age', 'desc')
                ->get();

            $this->assertEquals('Writer', $users[0]->profession);
        }
    }

}