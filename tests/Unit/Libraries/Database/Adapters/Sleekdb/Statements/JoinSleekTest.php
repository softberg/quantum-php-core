<?php

namespace Quantum\Shared\Models {

    use Quantum\Model\QtModel;

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

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Sleekdb\Statements {

    use Quantum\Tests\Unit\Libraries\Database\Adapters\Sleekdb\SleekDbalTestCase;
    use Quantum\Shared\Models\SleekUserProfessionModel;
    use Quantum\Shared\Models\SleekUserEventModel;
    use Quantum\Model\Exceptions\ModelException;
    use Quantum\Shared\Models\SleekMeetingModel;
    use Quantum\Shared\Models\SleekTicketModel;
    use Quantum\Shared\Models\SleekEventModel;
    use Quantum\Shared\Models\SleekNotesModel;
    use Quantum\Model\Factories\ModelFactory;
    use Quantum\Shared\Models\SleekUserModel;
    use Quantum\Model\ModelCollection;


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

            $this->assertInstanceOf(ModelCollection::class, $users);

            $this->assertIsArray($users->first()->prop('professions'));

            $this->assertIsArray($users->first()->prop('meetings'));
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

            $this->assertInstanceOf(ModelCollection::class, $users);

            $this->assertCount(2, $users);

            $this->assertIsArray($users->first()->prop('meetings')[0]);

            $this->assertArrayHasKey('tickets', $users->first()->prop('meetings')[0]);

            $this->assertIsArray($users->first()->prop('meetings')[0]['tickets'][0]);

            $this->assertArrayHasKey('notes', $users->first()->prop('meetings')[0]['tickets'][0]);
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

            $this->assertInstanceOf(ModelCollection::class, $users);

            $this->assertCount(1, $users);

            $this->assertEquals('Jane',$users->first()->prop('firstname'));

            $this->assertIsArray($users->first()->prop('professions'));

            $this->assertIsArray($users->first()->prop('meetings'));

            $this->assertEquals('Marketing', $users->first()->prop('meetings')[0]['title']);

            $this->assertArrayHasKey('tickets', $users->first()->prop('meetings')[0]);

            $this->assertIsArray($users->first()->prop('meetings')[0]['tickets']);
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

            $this->assertInstanceOf(ModelCollection::class, $users);

            $this->assertIsArray($users->first()->user_events);

            $this->assertArrayHasKey('confirmed', $users->first()->user_events[0]);

            $this->assertArrayHasKey('events', $users->first()->user_events[0]);

            $this->assertIsArray($users->first()->user_events[0]['events']);

            $this->assertArrayHasKey('title', $users->first()->user_events[0]['events'][0]);
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

            $this->assertInstanceOf(ModelCollection::class, $users);

            $this->assertIsArray($users->first()->professions);

            $this->assertArrayHasKey('title', $users->first()->professions[0]);

            $this->assertEquals('Singer', $users->first()->professions[0]['title']);
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

            $this->assertEquals('Writer', $users->first()->profession);
        }
    }
}