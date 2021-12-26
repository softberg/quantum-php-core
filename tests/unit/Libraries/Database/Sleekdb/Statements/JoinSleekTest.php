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

    use Quantum\Tests\Libraries\Database\Sleekdb\SleekDbalTestCase;
    use Quantum\Models\SleekUserProfessionModel;
    use Quantum\Models\SleekUserEventModel;
    use Quantum\Models\SleekMeetingModel;
    use Quantum\Models\SleekTicketModel;
    use Quantum\Models\SleekNotesModel;
    use Quantum\Models\SleekEventModel;
    use Quantum\Models\SleekUserModel;
    use Quantum\Factory\ModelFactory;

    /**
     * @runTestsInSeparateProcesses
     */
    class JoinSleekTest extends SleekDbalTestCase
    {

        public function testSleekSameLevelJoinTo()
        {
            $userModel = (new ModelFactory)->get(SleekUserModel::class);

            $userProfessionModel = (new ModelFactory)->get(SleekUserProfessionModel::class);

            $userMeetings = (new ModelFactory())->get(SleekMeetingModel::class);

            $users = $userModel
                ->joinTo($userProfessionModel, false)
                ->joinTo($userMeetings)
                ->get();

            $this->assertIsArray($users);

            $this->assertArrayHasKey('professions', $users[0]);

            $this->assertIsArray($users[0]['professions']);

            $this->assertArrayHasKey('meetings', $users[0]);

            $this->assertIsArray($users[0]['meetings']);
        }

        public function testSleekNestedLevelJoinTo()
        {
            $userModel = (new ModelFactory)->get(SleekUserModel::class);

            $meetingModel = (new ModelFactory)->get(SleekMeetingModel::class);

            $ticketModel = (new ModelFactory)->get(SleekTicketModel::class);

            $noteModel = (new ModelFactory)->get(SleekNotesModel::class);

            $users = $userModel
                ->joinTo($meetingModel)
                ->joinTo($ticketModel)
                ->joinTo($noteModel)
                ->get();

            $this->assertIsArray($users);

            $this->assertCount(2, $users);

            $this->assertArrayHasKey('meetings', $users[0]);

            $this->assertArrayHasKey('tickets', $users[0]['meetings'][0]);

            $this->assertArrayHasKey('notes', $users[0]['meetings'][0]['tickets'][0]);
        }

        public function testSleekMixedLevelJoinToWithCriteria()
        {
            $userModel = (new ModelFactory)->get(SleekUserModel::class);

            $userProfessionModel = (new ModelFactory)->get(SleekUserProfessionModel::class);

            $meetingModel = (new ModelFactory())->get(SleekMeetingModel::class);

            $ticketModel = (new ModelFactory)->get(SleekTicketModel::class);

            $users = $userModel
                ->joinTo($userProfessionModel, false)
                ->joinTo($meetingModel)
                ->joinTo($ticketModel)
                ->criteria('age', '=', 35)
                ->get();

            $this->assertIsArray($users);

            $this->assertCount(1, $users);

            $this->assertEquals('Jane', $users[0]['firstname']);

            $this->assertArrayHasKey('professions', $users[0]);

            $this->assertIsArray($users[0]['professions']);

            $this->assertArrayHasKey('meetings', $users[0]);

            $this->assertIsArray($users[0]['meetings'][0]);

            $this->assertEquals('Marketing', $users[0]['meetings'][0]['title']);

            $this->assertArrayHasKey('tickets', $users[0]['meetings'][0]);

            $this->assertIsArray($users[0]['meetings'][0]['tickets']);
        }

        public function testSleekJoinThrough()
        {
            $userModel = (new ModelFactory)->get(SleekUserModel::class);

            $userEventModel = (new ModelFactory)->get(SleekUserEventModel::class);

            $eventModel = (new ModelFactory)->get(SleekEventModel::class);

            $users = $userModel
                ->joinTo($userEventModel)
                ->joinThrough($eventModel)
                ->orderBy('title', 'asc')
                ->get();

            $this->assertIsArray($users);

            $this->assertArrayHasKey('user_events', $users[0]);

            $this->assertIsArray($users[0]['user_events']);

            $this->assertArrayHasKey('confirmed', $users[0]['user_events'][0]);

            $this->assertEquals('Yes', $users[0]['user_events'][0]['confirmed']);

            $this->assertArrayHasKey('events', $users[0]['user_events'][0]);

            $this->assertIsArray($users[0]['user_events'][0]['events']);

            $this->assertArrayHasKey('title', $users[0]['user_events'][0]['events'][0]);

            $this->assertEquals('Music', $users[0]['user_events'][0]['events'][0]['title']);
        }

        public function testSleekJoinToAndJoinThroughUsingSwitch()
        {
            $userModel = (new ModelFactory)->get(SleekUserModel::class);

            $userProfessionModel = (new ModelFactory)->get(SleekUserProfessionModel::class);

            $userEventModel = (new ModelFactory)->get(SleekUserEventModel::class);

            $eventModel = (new ModelFactory)->get(SleekEventModel::class);

            $users = $userModel
                ->joinTo($userProfessionModel, false)
                ->joinTo($userEventModel)
                ->joinThrough($eventModel)
                ->orderBy('title', 'asc')
                ->get();

            $this->assertIsArray($users);

            $this->assertArrayHasKey('professions', $users[0]);

            $this->assertIsArray($users[0]['professions']);

            $this->assertArrayHasKey('title', $users[0]['professions'][0]);

            $this->assertEquals('Singer', $users[0]['professions'][0]['title']);
        }
    }
}