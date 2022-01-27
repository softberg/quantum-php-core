<?php

namespace Quantum\Tests\Libraries\Database\Sleekdb;

use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Database\Sleekdb\SleekDbal;
use Quantum\Loader\Setup;
use Quantum\Di\Di;
use Quantum\App;


abstract class SleekDbalTestCase extends TestCase
{

    private $userModel;

    private $eventModel;

    private $userEventModel;

    private $professionModel;

    private $meetingModel;

    private $ticketModel;

    private $noteModel;

    public function setUp(): void
    {
        App::loadCoreFunctions(dirname(__DIR__, 5) . DS . 'src' . DS . 'Helpers');

        App::setBaseDir(dirname(__DIR__, 3) . DS . '_root');

        Di::loadDefinitions();

        config()->flush();

        config()->import(new Setup('shared' . DS . 'config', 'database'));

        config()->set('database.current', 'sleekdb');

        SleekDbal::connect(config()->get('database.sleekdb'));

        $this->_createUserTableWithData();

        $this->_createEventsTableWithData();

        $this->_createUserEventTableWithData();

        $this->_createProfessionTableWithData();

        $this->_createMeetingsTableWithData();

        $this->_createTicketsTableWithData();

        $this->_createNotesTableWithData();
    }

    public function tearDown(): void
    {
        config()->flush();

        $this->userModel->deleteTable();

        $this->eventModel->deleteTable();

        $this->userEventModel->deleteTable();

        $this->professionModel->deleteTable();

        $this->meetingModel->deleteTable();

        $this->ticketModel->deleteTable();

        $this->noteModel->deleteTable();

        SleekDbal::disconnect();
    }

    private function _createUserTableWithData()
    {
        $this->userModel = new SleekDbal('users');

        $this->userModel->create();
        $this->userModel->prop('firstname', 'John');
        $this->userModel->prop('lastname', 'Doe');
        $this->userModel->prop('age', 45);
        $this->userModel->prop('country', 'Ireland');
        $this->userModel->prop('created_at', date('Y-m-d H:i:s'));
        $this->userModel->save();

        $this->userModel->create();
        $this->userModel->prop('firstname', 'Jane');
        $this->userModel->prop('lastname', 'Du');
        $this->userModel->prop('age', 35);
        $this->userModel->prop('country', 'England');
        $this->userModel->prop('created_at', date('Y-m-d H:i:s'));
        $this->userModel->save();
    }

    private function _createEventsTableWithData()
    {
        $this->eventModel = new SleekDbal('events');

        $this->eventModel->create();
        $this->eventModel->prop('title', 'Dance');
        $this->eventModel->prop('country', 'New Zealand');
        $this->eventModel->prop('started_at', '2019-01-04 20:28:33');
        $this->eventModel->save();

        $this->eventModel->create();
        $this->eventModel->prop('title', 'Music');
        $this->eventModel->prop('country', 'England');
        $this->eventModel->prop('started_at', '2019-09-14 10:15:12');
        $this->eventModel->save();

        $this->eventModel->create();
        $this->eventModel->prop('title', 'Design');
        $this->eventModel->prop('country', 'Ireland');
        $this->eventModel->prop('started_at', '2020-02-14 10:15:12');
        $this->eventModel->save();

        $this->eventModel->create();
        $this->eventModel->prop('title', 'Music');
        $this->eventModel->prop('country', 'Ireland');
        $this->eventModel->prop('started_at', '2019-09-14 10:15:12');
        $this->eventModel->save();

        $this->eventModel->create();
        $this->eventModel->prop('title', 'Film');
        $this->eventModel->prop('country', 'Ireland');
        $this->eventModel->prop('started_at', '2040-02-14 10:15:12');
        $this->eventModel->save();

        $this->eventModel->create();
        $this->eventModel->prop('title', 'Art');
        $this->eventModel->prop('country', 'Island');
        $this->eventModel->prop('started_at', '2050-02-14 10:15:12');
        $this->eventModel->save();

        $this->eventModel->create();
        $this->eventModel->prop('title', 'Music');
        $this->eventModel->prop('country', 'Island');
        $this->eventModel->prop('started_at', '2030-02-14 10:15:12');
        $this->eventModel->save();
    }

    private function _createUserEventTableWithData()
    {

        $this->userEventModel = new SleekDbal('user_events');

        $this->userEventModel->create();
        $this->userEventModel->prop('user_id', 1);
        $this->userEventModel->prop('event_id', 1);
        $this->userEventModel->prop('confirmed', 'Yes');
        $this->userEventModel->prop('created_at', '2020-01-04 20:28:33');
        $this->userEventModel->save();

        $this->userEventModel->create();
        $this->userEventModel->prop('user_id', 1);
        $this->userEventModel->prop('event_id', 2);
        $this->userEventModel->prop('confirmed', 'No');
        $this->userEventModel->prop('created_at', '2020-02-19 05:15:12');
        $this->userEventModel->save();

        $this->userEventModel->create();
        $this->userEventModel->prop('user_id', 1);
        $this->userEventModel->prop('event_id', 4);
        $this->userEventModel->prop('confirmed', 'No');
        $this->userEventModel->prop('created_at', '2020-02-22 11:15:15');
        $this->userEventModel->save();

        $this->userEventModel->create();
        $this->userEventModel->prop('user_id', 2);
        $this->userEventModel->prop('event_id', 2);
        $this->userEventModel->prop('confirmed', 'Yes');
        $this->userEventModel->prop('created_at', '2020-03-10 02:17:12');
        $this->userEventModel->save();

        $this->userEventModel->create();
        $this->userEventModel->prop('user_id', 2);
        $this->userEventModel->prop('event_id', 3);
        $this->userEventModel->prop('confirmed', 'No');
        $this->userEventModel->prop('created_at', '2020-04-17 12:25:18');
        $this->userEventModel->save();

        $this->userEventModel->create();
        $this->userEventModel->prop('user_id', 2);
        $this->userEventModel->prop('event_id', 5);
        $this->userEventModel->prop('confirmed', 'No');
        $this->userEventModel->prop('created_at', '2020-04-15 11:10:12');
        $this->userEventModel->save();

        $this->userEventModel->create();
        $this->userEventModel->prop('user_id', 100);
        $this->userEventModel->prop('event_id', 200);
        $this->userEventModel->prop('confirmed', 'Yes');
        $this->userEventModel->prop('created_at', '2020-04-15 11:10:12');
        $this->userEventModel->save();

        $this->userEventModel->create();
        $this->userEventModel->prop('user_id', 110);
        $this->userEventModel->prop('event_id', 220);
        $this->userEventModel->prop('confirmed', 'No');
        $this->userEventModel->prop('created_at', '2020-04-15 11:10:12');
        $this->userEventModel->save();

    }

    private function _createProfessionTableWithData()
    {
        $this->professionModel = new SleekDbal('professions');

        $this->professionModel->create();
        $this->professionModel->prop('user_id', 1);
        $this->professionModel->prop('title', 'Writer');
        $this->professionModel->save();

        $this->professionModel->create();
        $this->professionModel->prop('user_id', 2);
        $this->professionModel->prop('title', 'Singer');
        $this->professionModel->save();
    }

    private function _createMeetingsTableWithData()
    {
        $this->meetingModel = new SleekDbal('meetings');

        $this->meetingModel->create();
        $this->meetingModel->prop('user_id', 1);
        $this->meetingModel->prop('title', 'Business planning');
        $this->meetingModel->prop('start_date', '2021-11-01 11:00:00');
        $this->meetingModel->save();

        $this->meetingModel->create();
        $this->meetingModel->prop('user_id', 1);
        $this->meetingModel->prop('title', 'Business management');
        $this->meetingModel->prop('start_date', '2021-11-05 11:00:00');
        $this->meetingModel->save();

        $this->meetingModel->create();
        $this->meetingModel->prop('user_id', 2);
        $this->meetingModel->prop('title', 'Marketing');
        $this->meetingModel->prop('start_date', '2021-11-10 13:00:00');
        $this->meetingModel->save();
    }

    private function _createTicketsTableWithData()
    {
        $this->ticketModel = new SleekDbal('tickets');

        $this->ticketModel->create();
        $this->ticketModel->prop('meeting_id', 1);
        $this->ticketModel->prop('type', 'regular');
        $this->ticketModel->prop('number', 'R1245');
        $this->ticketModel->save();

        $this->ticketModel->create();
        $this->ticketModel->prop('meeting_id', 1);
        $this->ticketModel->prop('type', 'regular');
        $this->ticketModel->prop('number', 'R4563');
        $this->ticketModel->save();

        $this->ticketModel->create();
        $this->ticketModel->prop('meeting_id', 1);
        $this->ticketModel->prop('type', 'vip');
        $this->ticketModel->prop('number', 'V4563');
        $this->ticketModel->save();

        $this->ticketModel->create();
        $this->ticketModel->prop('meeting_id', 2);
        $this->ticketModel->prop('type', 'vip');
        $this->ticketModel->prop('number', 'V7854');
        $this->ticketModel->save();

        $this->ticketModel->create();
        $this->ticketModel->prop('meeting_id', 3);
        $this->ticketModel->prop('type', 'vip');
        $this->ticketModel->prop('number', 'V7410');
        $this->ticketModel->save();

        $this->ticketModel->create();
        $this->ticketModel->prop('meeting_id', 3);
        $this->ticketModel->prop('type', 'vip');
        $this->ticketModel->prop('number', 'RT2233');
        $this->ticketModel->save();
    }

    private function _createNotesTableWithData()
    {
        $this->noteModel = new SleekDbal('notes');

        $this->noteModel->create();
        $this->noteModel->prop('ticket_id', 1);
        $this->noteModel->prop('note', 'note one');
        $this->noteModel->save();

        $this->noteModel->create();
        $this->noteModel->prop('ticket_id', 1);
        $this->noteModel->prop('note', 'note two');
        $this->noteModel->save();

        $this->noteModel->create();
        $this->noteModel->prop('ticket_id', 2);
        $this->noteModel->prop('note', 'note three');
        $this->noteModel->save();

        $this->noteModel->create();
        $this->noteModel->prop('ticket_id', 3);
        $this->noteModel->prop('note', 'note four');
        $this->noteModel->save();

        $this->noteModel->create();
        $this->noteModel->prop('ticket_id', 4);
        $this->noteModel->prop('note', 'note five');
        $this->noteModel->save();

        $this->noteModel->create();
        $this->noteModel->prop('ticket_id', 4);
        $this->noteModel->prop('note', 'note six');
        $this->noteModel->save();

        $this->noteModel->create();
        $this->noteModel->prop('ticket_id', 6);
        $this->noteModel->prop('note', 'note seven');
        $this->noteModel->save();
    }

}