<?php

namespace Quantum\Models {

    use Quantum\Mvc\QtModel;

    class CUserModel extends QtModel
    {
        public $table = 'users';
    }

    class CUserProfessionModel extends QtModel
    {
        public $table = 'user_professions';
        public $foreignKeys = [
            'users' => 'user_id'
        ];
    }

    class CUserEventModel extends QtModel
    {
        public $table = 'user_events';
        public $foreignKeys = [
            'users' => 'user_id',
            'events' => 'event_id'
        ];
    }

    class CEventModel extends QtModel
    {
        public $table = 'events';
        public $foreignKeys = [
            'user_events' => 'event_id'
        ];
    }

    class CMeetingModel extends QtModel
    {
        public $table = 'meetings';
        public $foreignKeys = [
            'users' => 'user_id'
        ];
    }

    class CTicketModel extends QtModel
    {
        public $table = 'tickets';
        public $foreignKeys = [
            'meetings' => 'meeting_id'
        ];
    }
}

namespace Quantum\Test\Unit {

    require_once dirname(__DIR__) . DS . 'IdiormDbalTestCase.php';

    use Quantum\Libraries\Database\Idiorm\IdiormDbal;
    use Quantum\Models\CUserProfessionModel;
    use Quantum\Models\CUserEventModel;
    use Quantum\Factory\ModelFactory;
    use Quantum\Models\CMeetingModel;
    use Quantum\Models\CTicketModel;
    use Quantum\Models\CEventModel;
    use Quantum\Models\CUserModel;

    class JoinIdiormTest extends IdiormDbalTestCase
    {
        public function testIdiormJoinAndInnerJoin()
        {
            $userModel = new IdiormDbal('users');

            $events = $userModel->join('user_events', ['user_events.user_id', '=', 'users.id'])->get();

            $this->assertCount(6, $events);

            $this->assertArrayHasKey('event_id', $events[0]);

            $userModel = new IdiormDbal('users');

            $events = $userModel->innerJoin('user_events', ['user_events.user_id', '=', 'users.id'])->get();

            $this->assertCount(6, $events);

            $this->assertArrayHasKey('event_id', $events[0]);
        }

        public function testIdiormMultipleJoins()
        {
            $userModel = new IdiormDbal('users');

            $result = $userModel->select('users.*', 'events.*')
                ->join('user_events', ['user_events.user_id', '=', 'users.id'])
                ->join('events', ['user_events.event_id', '=', 'events.id'])
                ->get();

            $this->assertCount(6, $result);

            $this->assertArrayHasKey('title', $result[0]);
        }

        public function testIdiormJoinWithCondition()
        {
            $userModel = new IdiormDbal('users');

            $result = $userModel->select('users.*', 'events.*')
                ->join('user_events', ['user_events.user_id', '=', 'users.id'])
                ->join('events', ['user_events.event_id', '=', 'events.id'])
                ->criteria('events.started_at', '>=', '2020-01-01')
                ->get();

            $this->assertCount(3, $result);
        }

        /** Right join can not be tested this time because the sqlite does not support it */
        public function testIdiormLeftJoinAndRightJoin()
        {
            $userModel = new IdiormDbal('user_events');

            $events = $userModel->innerJoin('events', ['user_events.event_id', '=', 'events.id'])->get();

            $this->assertCount(6, $events);

            $userModel = new IdiormDbal('user_events');

            $events = $userModel->leftJoin('events', ['user_events.event_id', '=', 'events.id'])->get();

            $this->assertCount(8, $events);

            $this->assertNull($events[count($events) - 1]['id']);
        }

        public function testIdiormJoinTo()
        {
            $userModel = (new ModelFactory)->get(CUserModel::class);

            $userProfessionModel = (new ModelFactory)->get(CUserProfessionModel::class);

            $users = $userModel->select(['users.id' => 'user_id'],
                'firstname', 'user_professions.title')
                ->joinTo($userProfessionModel)
                ->get();

            $this->assertIsArray($users);

            $this->assertCount(2, $users);

            $this->assertArrayHasKey('title', $users[0]);

            $query = "SELECT `users`.`id` AS `user_id`, `firstname`, `user_professions`.`title` 
                            FROM `users` 
                                JOIN `user_professions` ON `user_professions`.`user_id` = `users`.`id`";

            $query = preg_replace('/[\s\t]+/', ' ', preg_replace('/' . PHP_EOL . '+/', '', $query));

            $this->assertEquals($query, IdiormDbal::lastQuery());
        }

        public function testIdiormJoinToFromSameTable()
        {
            $userModel = (new ModelFactory)->get(CUserModel::class);

            $userProfessionModel = (new ModelFactory)->get(CUserProfessionModel::class);

            $userEventModel = (new ModelFactory)->get(CUserEventModel::class);

            $users = $userModel->select('users.*', 'user_professions.title', 'user_events.event_id')
                ->joinTo($userProfessionModel, false)
                ->joinTo($userEventModel, false)
                ->get();

            $this->assertIsArray($users);

            $this->assertArrayHasKey('title', $users[0]);

            $this->assertArrayHasKey('event_id', $users[0]);

            $query = "SELECT `users`.*, `user_professions`.`title`, `user_events`.`event_id` 
                            FROM `users` 
                                JOIN `user_professions` ON `user_professions`.`user_id` = `users`.`id` 
                                JOIN `user_events` ON `user_events`.`user_id` = `users`.`id`";

            $query = preg_replace('/[\s\t]+/', ' ', preg_replace('/' . PHP_EOL . '+/', '', $query));

            $this->assertEquals($query, IdiormDbal::lastQuery());
        }

        public function testIdiormJoinToWithTableSwitch()
        {
            $userModel = (new ModelFactory)->get(CUserModel::class);

            $meetingModel = (new ModelFactory)->get(CMeetingModel::class);

            $ticketModel = (new ModelFactory)->get(CTicketModel::class);

            $users = $userModel
                ->joinTo($meetingModel)
                ->joinTo($ticketModel)
                ->get();

            $this->assertIsArray($users);

            $this->assertArrayHasKey('title', $users[0]);

            $this->assertArrayHasKey('number', $users[0]);

            $query = "SELECT * FROM `users` 
                        JOIN `meetings` ON `meetings`.`user_id` = `users`.`id` 
                        JOIN `tickets` ON `tickets`.`meeting_id` = `meetings`.`id`";

            $query = preg_replace('/[\s\t]+/', ' ', preg_replace('/' . PHP_EOL . '+/', '', $query));

            $this->assertEquals($query, IdiormDbal::lastQuery());
        }

        public function testIdiormJoinThrough()
        {
            $userModel = (new ModelFactory)->get(CUserModel::class);

            $userEventModel = (new ModelFactory)->get(CUserEventModel::class);

            $eventModel = (new ModelFactory)->get(CEventModel::class);

            $users = $userModel->select(
                ['users.id' => 'user_id'],
                ['events.id' => 'event_id'],
                'firstname',
                'confirmed',
                ['events.title' => 'event_title'])
                ->joinTo($userEventModel)
                ->joinThrough($eventModel)
                ->criteria('user_events.confirmed', '=', 'Yes')
                ->orderBy('user_events.created_at', 'desc')
                ->get();

            $this->assertIsArray($users);

            $this->assertArrayHasKey('confirmed', $users[0]);

            $this->assertEquals('Yes', $users[0]['confirmed']);

            $query = "SELECT `users`.`id` AS `user_id`, 
                             `events`.`id` AS `event_id`, 
                             `firstname`, 
                             `confirmed`, 
                             `events`.`title` AS `event_title` 
                        FROM `users` 
                            JOIN `user_events` ON `user_events`.`user_id` = `users`.`id` 
                            JOIN `events` ON `events`.`id` = `user_events`.`event_id` 
                        WHERE `user_events`.`confirmed` = 'Yes' 
                        ORDER BY `user_events`.`created_at` DESC";

            $query = preg_replace('/[\s\t]+/', ' ', preg_replace('/' . PHP_EOL . '+/', '', $query));

            $this->assertEquals($query, IdiormDbal::lastQuery());

        }

        public function testIdiormJoinToAndJoinThrough()
        {
            $userModel = (new ModelFactory)->get(CUserModel::class);

            $userProfessionModel = (new ModelFactory)->get(CUserProfessionModel::class);

            $userEventModel = (new ModelFactory)->get(CUserEventModel::class);

            $eventModel = (new ModelFactory)->get(CEventModel::class);

            $user = $userModel->select(
                ['users.id' => 'user_id'],
                'firstname',
                ['user_professions.title' => 'profession_title'],
                ['events.title' => 'event_title'])
                ->joinTo($userProfessionModel, false)
                ->joinTo($userEventModel)
                ->joinThrough($eventModel)
                ->first();

            $this->assertEquals('John', $user->firstname);

            $this->assertEquals('Writer', $user->profession_title);

            $this->assertEquals('Dance', $user->event_title);

            $query = "SELECT `users`.`id` AS `user_id`,
                    `firstname`, `user_professions`.`title` AS `profession_title`, 
                    `events`.`title` AS `event_title` 
                FROM `users` 
                    JOIN `user_professions` ON `user_professions`.`user_id` = `users`.`id` 
                    JOIN `user_events` ON `user_events`.`user_id` = `users`.`id` 
                    JOIN `events` ON `events`.`id` = `user_events`.`event_id` 
                    LIMIT 1";

            $query = preg_replace('/[\s\t]+/', ' ', preg_replace('/' . PHP_EOL . '+/', '', $query));

            $this->assertEquals($query, IdiormDbal::lastQuery());
        }
    }
}