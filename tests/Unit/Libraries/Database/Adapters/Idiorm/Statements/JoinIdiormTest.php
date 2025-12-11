<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm\IdiormDbalTestCase;
use Quantum\Tests\_root\shared\Models\TestUserProfessionModel;
use Quantum\Tests\_root\shared\Models\TestUserMeetingModel;
use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Tests\_root\shared\Models\TestUserEventModel;
use Quantum\Tests\_root\shared\Models\TestTicketModel;
use Quantum\Tests\_root\shared\Models\TestEventModel;
use Quantum\Tests\_root\shared\Models\TestUserModel;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Model\ModelCollection;

class JoinIdiormTest extends IdiormDbalTestCase
{

    public function testIdiormJoinAndInnerJoin()
    {
        $userModel = new IdiormDbal('users');

        $events = $userModel->join('user_events', ['user_events.user_id', '=', 'users.id'])->get();

        $this->assertCount(6, $events);

        $this->assertEquals(1, $events[0]->prop('event_id'));

        $userModel = new IdiormDbal('users');

        $events = $userModel->innerJoin('user_events', ['user_events.user_id', '=', 'users.id'])->get();

        $this->assertCount(6, $events);

        $this->assertEquals(1, $events[0]->prop('event_id'));
    }

    public function testIdiormMultipleJoins()
    {
        $userModel = new IdiormDbal('users');

        $result = $userModel->select('users.*', 'events.*')
            ->join('user_events', ['user_events.user_id', '=', 'users.id'])
            ->join('events', ['user_events.event_id', '=', 'events.id'])
            ->get();

        $this->assertCount(6, $result);

        $this->assertEquals('Dance', $result[0]->prop('title'));
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

        $this->assertNull($events[count($events) - 1]->prop('id'));
    }

    public function testIdiormJoinTo()
    {
        $userModel = ModelFactory::get(TestUserModel::class);

        $userProfessionModel = ModelFactory::get(TestUserProfessionModel::class);

        $users = $userModel->select(['users.id' => 'user_id'],
            'firstname', 'user_professions.title')
            ->joinTo($userProfessionModel)
            ->get();

        $this->assertInstanceOf(ModelCollection::class, $users);

        $this->assertCount(2, $users);

        $this->assertEquals('Writer', $users->first()->prop('title'));

        $expectedQuery = "SELECT `users`.`id` AS `user_id`, `firstname`, `user_professions`.`title` 
                            FROM `users` 
                                JOIN `user_professions` ON `user_professions`.`user_id` = `users`.`id`";

        $expectedQuery = preg_replace('/[\s\t]+/', ' ', preg_replace('/' . PHP_EOL . '+/', '', $expectedQuery));

        $this->assertEquals($expectedQuery, IdiormDbal::lastQuery());
    }

    public function testIdiormJoinToFromSameTable()
    {
        $userModel = ModelFactory::get(TestUserModel::class);

        $userProfessionModel = ModelFactory::get(TestUserProfessionModel::class);

        $userEventModel = ModelFactory::get(TestUserEventModel::class);

        $users = $userModel->select('users.*', 'user_professions.title', 'user_events.event_id')
            ->joinTo($userProfessionModel, false)
            ->joinTo($userEventModel, false)
            ->get();

        $this->assertInstanceOf(ModelCollection::class, $users);

        $this->assertEquals('Writer', $users->first()->prop('title'));

        $this->assertEquals(1, $users->first()->prop('event_id'));

        $query = "SELECT `users`.*, `user_professions`.`title`, `user_events`.`event_id` 
                            FROM `users` 
                                JOIN `user_professions` ON `user_professions`.`user_id` = `users`.`id` 
                                JOIN `user_events` ON `user_events`.`user_id` = `users`.`id`";

        $query = preg_replace('/[\s\t]+/', ' ', preg_replace('/' . PHP_EOL . '+/', '', $query));

        $this->assertEquals($query, IdiormDbal::lastQuery());
    }

    public function testIdiormJoinToWithTableSwitch()
    {
        $userModel = ModelFactory::get(TestUserModel::class);

        $meetingModel = ModelFactory::get(TestUserMeetingModel::class);

        $ticketModel = ModelFactory::get(TestTicketModel::class);

        $users = $userModel
            ->joinTo($meetingModel)
            ->joinTo($ticketModel)
            ->get();

        $this->assertInstanceOf(ModelCollection::class, $users);

        $this->assertEquals('Business planning', $users->first()->prop('title'));

        $this->assertNull($users->first()->prop('event_id'));

        $query = "SELECT * FROM `users` 
                        JOIN `user_meetings` ON `user_meetings`.`user_id` = `users`.`id` 
                        JOIN `tickets` ON `tickets`.`meeting_id` = `user_meetings`.`id`";

        $query = preg_replace('/[\s\t]+/', ' ', preg_replace('/' . PHP_EOL . '+/', '', $query));

        $this->assertEquals($query, IdiormDbal::lastQuery());
    }

    public function testIdiormJoinToAndThrough()
    {
        $userModel = ModelFactory::get(TestUserModel::class);

        $userEventModel = ModelFactory::get(TestUserEventModel::class);

        $eventModel = ModelFactory::get(TestEventModel::class);

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

        $this->assertInstanceOf(ModelCollection::class, $users);

        $this->assertEquals('Yes', $users->first()->prop('confirmed'));

        $this->assertEquals('Music', $users->first()->prop('event_title'));

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

    public function testIdiormJoinThroughInverse()
    {
        $meetingModel = ModelFactory::get(TestUserMeetingModel::class);

        $ticketModel = ModelFactory::get(TestTicketModel::class);

        $tickets = $ticketModel->joinThrough($meetingModel)->get();

        $this->assertInstanceOf(ModelCollection::class, $tickets);

        $query = "SELECT * FROM `tickets` JOIN `user_meetings` ON `user_meetings`.`id` = `tickets`.`meeting_id`";

        $query = preg_replace('/[\s\t]+/', ' ', preg_replace('/' . PHP_EOL . '+/', '', $query));

        $this->assertEquals($query, IdiormDbal::lastQuery());
    }

    public function testIdiormJoinToAndJoinThrough()
    {
        $userModel = ModelFactory::get(TestUserModel::class);

        $userProfessionModel = ModelFactory::get(TestUserProfessionModel::class);

        $userEventModel = ModelFactory::get(TestUserEventModel::class);

        $eventModel = ModelFactory::get(TestEventModel::class);

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

    public function testIdiormWrongRelation()
    {
        $this->expectException(ModelException::class);

        $this->expectExceptionMessage('The model `' . TestTicketModel::class . '` does not define relation with `' . TestEventModel::class . '`');

        $eventModel = ModelFactory::get(TestEventModel::class);

        $ticketModel = ModelFactory::get(TestTicketModel::class);

        $eventModel->joinTo($ticketModel)->get();
    }

    public function testIdiormSelectFieldsAtJoin()
    {
        $userModel = ModelFactory::get(TestUserModel::class);

        $userProfessionModel = ModelFactory::get(TestUserProfessionModel::class);

        $users = $userModel
            ->joinTo($userProfessionModel, false)
            ->select('firstname', 'lastname', 'age', 'country', ['user_professions.title' => 'profession'])
            ->orderBy('age', 'desc')
            ->get();

        $this->assertInstanceOf(ModelCollection::class, $users);

        $this->assertEquals('Writer', $users->first()->prop('profession'));
    }
}