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

}

namespace Quantum\Test\Unit {

    use Mockery;
    use PHPUnit\Framework\TestCase;
    use Quantum\Loader\Loader;
    use Quantum\Models\CUserModel;
    use Quantum\Models\CEventModel;
    use Quantum\Models\CUserEventModel;
    use Quantum\Models\CUserProfessionModel;
    use Quantum\Libraries\Database\IdiormDbal;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class IdiormDbalTest extends TestCase
    {

        private $dbConfigs = [
            'current' => 'sqlite',
            'mysql' => array(
                'driver' => 'mysql',
                'host' => 'localhost',
                'dbname' => 'database',
                'username' => 'username',
                'password' => 'password',
                'charset' => 'charset',
            ),
            'sqlite' => array(
                'driver' => 'sqlite',
                'database' => ':memory:'
            ),
        ];

        public function setUp(): void
        {
            (new idiormDbal('users'))->execute("CREATE TABLE users (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )");

            (new idiormDbal('user_professions'))->execute("CREATE TABLE user_professions (
                        id INTEGER PRIMARY KEY,
                        user_id INTEGER(11),
                        title VARCHAR(255)
                    )");

            (new idiormDbal('user_professions'))->execute("INSERT INTO 
                    user_professions
                        (user_id, title) 
                    VALUES
                        (1, 'Writer'), 
                        (2, 'Singer')
                    ");

            (new idiormDbal('users'))->execute("INSERT INTO 
                    users
                        (firstname, lastname, age, country, created_at) 
                    VALUES
                        ('John', 'Doe', 45, 'Ireland', '2020-01-04 20:28:33'), 
                        ('Jane', 'Du', 35, 'England', '2020-02-14 10:15:12')
                    ");

            (new idiormDbal('events'))->execute("CREATE TABLE events (
                        id INTEGER PRIMARY KEY,
                        title VARCHAR(255),
                        country VARCHAR(255),
                        started_at DATETIME
                    )");

            (new idiormDbal('events'))->execute("INSERT INTO 
                    events
                        (title, country, started_at) 
                    VALUES
                        ('Dance', 'New Zealand', '2019-01-04 20:28:33'), 
                        ('Music', 'England', '2019-09-14 10:15:12'),
                        ('Design', 'Ireland', '2020-02-14 10:15:12'),
                        ('Music', 'Ireland', '2019-09-14 10:15:12'),
                        ('Film', 'Ireland', '2040-02-14 10:15:12'),
                        ('Art', 'Island', '2050-02-14 10:15:12'),
                        ('Music', 'Island', '2030-02-14 10:15:12')
                    ");

            (new idiormDbal('user_events'))->execute("CREATE TABLE user_events (
                        id INTEGER PRIMARY KEY,
                        user_id INTEGER(11),
                        event_id INTEGER(11),
                        created_at DATETIME
                    )");

            (new idiormDbal('user_events'))->execute("INSERT INTO 
                    user_events
                        (user_id, event_id, created_at) 
                    VALUES
                        (1, 1, '2020-01-04 20:28:33'), 
                        (1, 2, '2020-02-19 05:15:12'),
                        (1, 4, '2020-02-22 11:15:15'),
                        (2, 2, '2020-03-10 02:17:12'),
                        (2, 3, '2020-04-17 12:25:18'),
                        (2, 5, '2020-04-15 11:10:12'),
                        (100, 200, '2020-04-15 11:10:12'),
                        (110, 220, '2020-04-15 11:10:12')
                    ");

            $loader = new Loader();

            $loader->loadDir(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers' . DS . 'functions');
        }

        public function tearDown(): void
        {
            Mockery::close();
        }

        public function testIdiormDbalConstructor()
        {
            $userModel = new IdiormDbal('users');

            $this->assertInstanceOf(IdiormDbal::class, $userModel);
        }

        public function testGetTable()
        {
            $userModel = new IdiormDbal('users');

            $this->assertEquals('users', $userModel->getTable());
        }

        public function testDbConnect()
        {
            $this->assertIsArray(IdiormDbal::dbConnect($this->dbConfigs['sqlite']));
        }

        public function testFindOne()
        {
            $userModel = new IdiormDbal('users');

            $user = $userModel->findOne(1);

            $this->assertEquals('John', $user->firstname);

            $this->assertEquals('Doe', $user->lastname);
        }

        public function testFindOneBy()
        {
            $userModel = new IdiormDbal('users');

            $user = $userModel->findOneBy('firstname', 'John');

            $this->assertEquals('Doe', $user->lastname);

            $this->assertEquals('45', $user->age);
        }

        public function testFirst()
        {
            $userModel = new IdiormDbal('users');

            $user = $userModel->first();

            $this->assertEquals('Doe', $user->lastname);

            $this->assertEquals('45', $user->age);

            $this->assertNotEquals('Dane', $user->lastname);
        }

        public function testAsArray()
        {
            $userModel = new IdiormDbal('users');

            $user = $userModel->first();

            $this->assertIsObject($user);

            $this->assertIsArray($user->asArray());
        }

        public function testCount()
        {
            $userModel = new IdiormDbal('users');

            $userCount = $userModel->count();

            $this->assertIsInt($userCount);

            $this->assertEquals(2, $userCount);
        }

        public function testGet()
        {
            $userModel = new IdiormDbal('users');

            $users = $userModel->get();

            $this->assertIsArray($users);

            $this->assertEquals('John', $users[0]['firstname']);

            $this->assertEquals('Jane', $users[1]['firstname']);

            $users = $userModel->get('object');

            $this->assertEquals('John', $users[0]->firstname);

            $this->assertEquals('Jane', $users[1]->firstname);
        }

        public function testSelect()
        {
            $userModel = new IdiormDbal('users');

            $userModel->select('id', 'age');

            $user = $userModel->first();

            $this->assertCount(2, $user->asArray());

            $userModel = new IdiormDbal('users');

            $userModel->select('id', ['firstname' => 'name'], ['lastname' => 'surname']);

            $user = $userModel->first();

            $this->assertEquals('John', $user->name);

            $this->assertEquals('Doe', $user->surname);
        }

        /** Method chaining is not working here * */
        public function testCriteriaEquals()
        {
            $userModel = new IdiormDbal('users');

            $userModel->criteria('firstname', '=', 'John');

            $user = $userModel->first();

            $this->assertEquals('John', $user->firstname);
        }

        /** Method chaining is not working here * */
        public function testCriteriaNotEquals()
        {
            $userModel = new IdiormDbal('users');

            $userModel->criteria('firstname', '!=', 'John');

            $user = $userModel->first();

            $this->assertEquals('Jane', $user->firstname);
        }

        /** Method chaining is not working here * */
        public function testCriteriaGreaterAndGreaterOrEqual()
        {
            $userModel = new IdiormDbal('users');

            $userModel->criteria('age', '>', 45);

            $this->assertCount(0, $userModel->get());

            $userModel = new IdiormDbal('users');

            $userModel->criteria('age', '>=', 45);

            $this->assertCount(1, $userModel->get());

            $user = $userModel->first();

            $this->assertEquals('John', $user->firstname);
        }

        /** Method chaining is not working here * */
        public function testCriteriaSmalerAndSmallerOrEqual()
        {
            $userModel = new IdiormDbal('users');

            $userModel->criteria('age', '<', 35);

            $this->assertCount(0, $userModel->get());

            $userModel = new IdiormDbal('users');

            $userModel->criteria('age', '<=', 35);

            $this->assertCount(1, $userModel->get());

            $user = $userModel->first();

            $this->assertEquals('Jane', $user->firstname);
        }

        /** Method chaining is not working here * */
        public function testCriteriaInAndNotIn()
        {
            $userModel = new IdiormDbal('users');

            $userModel->criteria('age', 'IN', [35, 40, 45]);

            $users = $userModel->get();

            $this->assertCount(2, $users);

            $this->assertEquals('John', $users[0]['firstname']);

            $this->assertEquals('Jane', $users[1]['firstname']);

            $userModel = new IdiormDbal('users');

            $userModel->criteria('age', 'NOT IN', [30, 40, 45]);

            $users = $userModel->get();

            $this->assertCount(1, $users);

            $this->assertEquals('Jane', $users[0]['firstname']);
        }

        /** Method chaining is not working here * */
        public function testCriteriaLikeAndNotLike()
        {
            $userModel = new IdiormDbal('users');

            $userModel->criteria('firstname', 'LIKE', '%Jo%');

            $users = $userModel->get();

            $this->assertCount(1, $users);

            $this->assertEquals('John', $users[0]['firstname']);

            $userModel = new IdiormDbal('users');

            $userModel->criteria('firstname', 'LIKE', '%J%');

            $users = $userModel->get();

            $this->assertCount(2, $users);

            $this->assertEquals('Jane', $users[1]['firstname']);

            $userModel = new IdiormDbal('users');

            $userModel->criteria('firstname', 'NOT LIKE', '%Jo%');

            $users = $userModel->get();

            $this->assertCount(1, $users);

            $this->assertEquals('Jane', $users[0]['firstname']);
        }

        /** Method chaining is not working here * */
        public function testCriteriaNullAndNotNull()
        {
            $userModel = new IdiormDbal('users');

            $userModel->criteria('firstname', 'NULL', '');

            $users = $userModel->get();

            $this->assertCount(0, $users);

            $userModel = new IdiormDbal('users');

            $userModel->criteria('firstname', 'NOT NULL', '');

            $users = $userModel->get();

            $this->assertCount(2, $users);

            $this->assertEquals('John', $users[0]['firstname']);

            $this->assertEquals('Jane', $users[1]['firstname']);
        }

        /** Method chaining is not working here * */
        public function testCriteriaWithFunction()
        {
            $eventModel = new IdiormDbal('events');

            $eventModel->criteria('started_at', '>=', ['fn' => 'date("now")']);

            $events = $eventModel->get();

            $this->assertCount(3, $events);

            /** Tested at 2020-04-05 01:05:00 */
            $this->assertEquals('2040-02-14 10:15:12', $events[0]['started_at']);

            $this->assertEquals('2050-02-14 10:15:12', $events[1]['started_at']);
        }

        /** Method chaining is not working here * */
        public function testCriteriaColumnsEqual()
        {
            $userModel = new IdiormDbal('users');

            $userModel->join('user_events', ['user_events.user_id', '=', 'users.id']);

            $userModel->join('events', ['user_events.event_id', '=', 'events.id']);

            $userModel->criteria('users.country', '#=#', 'events.country');

            $userEvents = $userModel->get();

            $this->assertCount(2, $userEvents);

            $this->assertEquals('John', $userEvents[0]['firstname']);

            $this->assertEquals('Music', $userEvents[0]['title']);

            $this->assertEquals('Ireland', $userEvents[0]['country']);

            $this->assertEquals('Jane', $userEvents[1]['firstname']);

            $this->assertEquals('Music', $userEvents[1]['title']);

            $this->assertEquals('England', $userEvents[1]['country']);
        }

        /** Method chaining is not working here  */
        public function testMultipleAndCriterias()
        {
            $eventsModel = new IdiormDbal('events');

            $eventsModel->criterias(['title', '=', 'Music'], ['country', '=', 'Island'], ['started_at', '>=', ['fn' => 'date("now")']]);

            $events = $eventsModel->get();

            $this->assertEquals('Music', $events[0]['title']);

            $this->assertEquals('Island', $events[0]['country']);
        }

        /** Method chaining is not working here  */
        public function testMultipleOrCriterias()
        {
            $eventsModel = new IdiormDbal('events');

            $eventsModel->criterias([['title', '=', 'Music'], ['title', '=', 'Dance'], ['title', '=', 'Art']]);

            $eventsModel->groupBy('title');

            $events = $eventsModel->get();

            $this->assertEquals('Art', $events[0]['title']);

            $this->assertEquals('Dance', $events[1]['title']);

            $this->assertEquals('Music', $events[2]['title']);
        }

        /** Method chaining is not working here  */
        public function testOrderBy()
        {
            $eventsModel = new IdiormDbal('events');

            $eventsModel->orderBy('title', 'asc');

            $events = $eventsModel->get();

            $this->assertEquals('Art', $events[0]['title']);

            $this->assertEquals('Music', $events[count($events) - 1]['title']);
        }

        /** Method chaining is not working here  */
        public function testGroupBy()
        {
            $eventsModel = new IdiormDbal('events');

            $eventsModel->groupBy('country');

            $events = $eventsModel->get();

            $this->assertCount(4, $events);

            $eventsModel = new IdiormDbal('events');

            $eventsModel->groupBy('title');

            $events = $eventsModel->get();

            $this->assertCount(5, $events);
        }

        /** Method chaining is not working here  */
        public function testLimitAndOffset()
        {
            $eventsModel = new IdiormDbal('events');

            $eventsModel->limit(3);

            $events = $eventsModel->get();

            $this->assertCount(3, $events);

            $this->assertEquals(1, $events[0]['id']);

            $eventsModel = new IdiormDbal('events');

            $eventsModel->offset(1);

            $eventsModel->limit(3);

            $events = $eventsModel->get();

            $this->assertCount(3, $events);

            $this->assertEquals(2, $events[0]['id']);
        }

        /** Method chaining is not working here  */
        public function testCreateNewRecord()
        {
            $eventsModel = new IdiormDbal('events');

            $this->assertEquals(7, $eventsModel->count());

            $event = $eventsModel->create();

            $event->title = 'Biking';

            $event->country = 'New Zeland';

            $event->started_at = '2020-07-11 11:00:00';

            $event->save();

            $this->assertEquals(8, $eventsModel->count());

            $eventsModel->criteria('title', '=', 'Biking');

            $bikingEvent = $eventsModel->first();

            $this->assertEquals('Biking', $bikingEvent->title);

            $this->assertEquals('New Zeland', $bikingEvent->country);

            $this->assertEquals('2020-07-11 11:00:00', $bikingEvent->started_at);
        }

        /** Method chaining is not working here  */
        public function testUpdateExistingRecord()
        {
            $eventsModel = new IdiormDbal('events');

            $event = $eventsModel->findOne(1);

            $this->assertEquals('Dance', $event->title);

            $event->title = 'Climbing';

            $event->save();

            $event = $eventsModel->findOne(1);

            $this->assertEquals('Climbing', $event->title);
        }

        /** Method chaining is not working here  */
        public function testDeleteRecord()
        {
            $eventsModel = new IdiormDbal('events');

            $this->assertEquals(7, $eventsModel->count());

            $event = $eventsModel->findOne(1);

            $event->delete();

            $eventsModel = new IdiormDbal('events');

            $this->assertEquals(6, $eventsModel->count());

            $event = $eventsModel->findOne(1);

            $this->assertNull($event->title);
        }

        /** Method chaining is not working here  */
        public function testDeleteAll()
        {
            $eventsModel = new IdiormDbal('events');

            $this->assertEquals(7, $eventsModel->count());

            $eventsModel->criteria('title', '=', 'Dance');

            $eventsModel->deleteAll();

            $eventsModel = new IdiormDbal('events');

            $this->assertCount(6, $eventsModel->get());

            $eventsModel->criteria('title', '=', 'Dance');

            $this->assertEmpty($eventsModel->get());
        }

        /** Method chaining is not working here  */
        public function testJoinAndInnerJoin()
        {
            $userModel = new IdiormDbal('users');

            $userModel->join('user_events', ['user_events.user_id', '=', 'users.id']);

            $events = $userModel->get();

            $this->assertCount(6, $events);

            $this->assertArrayHasKey('event_id', $events[0]);

            $userModel = new IdiormDbal('users');

            $userModel->innerJoin('user_events', ['user_events.user_id', '=', 'users.id']);

            $events = $userModel->get();

            $this->assertCount(6, $events);

            $this->assertArrayHasKey('event_id', $events[0]);
        }

        /** Method chaining is not working here  */

        /** Right join can not be tested this time because the sqlite does not support */
        public function testLeftJoinAndRightJoin()
        {
            $userModel = new IdiormDbal('user_events');

            $userModel->innerJoin('events', ['user_events.event_id', '=', 'events.id']);

            $events = $userModel->get();

            $this->assertCount(6, $events);

            $userModel = new IdiormDbal('user_events');

            $userModel->leftJoin('events', ['user_events.event_id', '=', 'events.id']);

            $events = $userModel->get();

            $this->assertCount(8, $events);

            $this->assertNull($events[count($events) - 1]['id']);
        }

        /** Method chaining is not working here  */
        public function testJoinToAndJoinThrough()
        {
            $this->databaseMock = Mockery::mock('overload:Quantum\Libraries\Database\Database');

            $this->databaseMock->shouldReceive('getORM')->andReturn(new IdiormDbal('users'));

            $userModel = new CUserModel();

            $userProfessionModel = new CUserProfessionModel();

            $userEventModel = new CUserEventModel();

            $eventModel = new CEventModel();

            $userModel->select(['users.id' => 'usr_id'], 'firstname', ['user_professions.title' => 'profession_title'], ['events.title' => 'event_title']);

            $userModel->joinTo($userProfessionModel, false);

            $userModel->joinTo($userEventModel);

            $userModel->joinThrough($eventModel);

            $user = $userModel->first();

            $this->assertEquals('John', $user->firstname);

            $this->assertEquals('Writer', $user->profession_title);

            $this->assertEquals('Dance', $user->event_title);
        }

        /** Method chaining is not working here  */
        public function testExecute()
        {
            $eventModel = new IdiormDbal('events');

            $event = $eventModel->findOne(1);

            $this->assertEquals('Dance', $event->title);

            $eventModel->execute('UPDATE events SET title=:title WHERE id=:id', ['title' => 'Singing', 'id' => 1]);

            $event = $eventModel->findOne(1);

            $this->assertEquals('Singing', $event->title);
        }

        /** Method chaining is not working here  */
        public function testQuery()
        {
            $eventModel = new IdiormDbal('events');

            $events = $eventModel->query('SELECT * FROM events WHERE started_at BETWEEN :date_from AND :date_to', ['date_from' => '2035-02-14 10:15:12', 'date_to' => '2045-02-14 10:15:12']);

            $this->assertEquals('Film', $events[0]['title']);

            $this->assertEquals('Ireland', $events[0]['country']);

            $this->assertEquals('2040-02-14 10:15:12', $events[0]['started_at']);
        }

        /** Method chaining is not working here  */

        /** Works only if debug set to true */
        public function testLastQuery()
        {
            config()->set('debug', true);

            IdiormDbal::dbConnect($this->dbConfigs['sqlite']);

            $eventModel = new IdiormDbal('events');

            $eventModel->criteria('country', '=', 'Ireland');

            $eventModel->get();

            $this->assertEquals("SELECT * FROM `events` WHERE `country` = 'Ireland'", $eventModel::lastQuery());
        }

        /** Method chaining is not working here  */
        public function testLastStatement()
        {
            IdiormDbal::dbConnect($this->dbConfigs['sqlite']);

            $eventModel = new IdiormDbal('events');

            $eventModel->criteria('country', '=', 'Ireland');

            $eventModel->get();

            $this->assertEquals("SELECT * FROM `events` WHERE `country` = ?", $eventModel::lastStatement()->queryString);
        }

        /** Method chaining is not working here  */
        public function testQueryLog()
        {
            config()->set('debug', true);

            IdiormDbal::dbConnect($this->dbConfigs['sqlite']);

            $eventModel = new IdiormDbal('events');

            $eventModel->criteria('country', '=', 'Ireland');

            $eventModel->get();

            $userModel = new IdiormDbal('users');

            $userModel->get();

            $this->assertCount(2, $eventModel::queryLog());

            $this->assertIsArray($eventModel::queryLog());

            $this->assertEquals("SELECT * FROM `users`", $eventModel::queryLog()[1]);
        }

    }

}
