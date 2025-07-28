<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters;

class DatabaseSeeder
{
    private $userModel;
    private $userProfessionModel;
    private $eventModel;
    private $userEventModel;
    private $userMeetingModel;
    private $ticketModel;
    private $noteModel;

    public function __construct(string $dbalClass)
    {
        $this->userModel = new $dbalClass('users');
        $this->userProfessionModel = new $dbalClass('user_professions');
        $this->eventModel = new $dbalClass('events');
        $this->userEventModel = new $dbalClass('user_events');
        $this->userMeetingModel = new $dbalClass('user_meetings');
        $this->ticketModel = new $dbalClass('tickets');
        $this->noteModel = new $dbalClass('notes');
    }

    public function seed(): void
    {
        $this->seedUsers();
        $this->seedUserProfessions();
        $this->seedEvents();
        $this->seedUserEvents();
        $this->seedUserMeetings();
        $this->seedTickets();
        $this->seedNotes();
    }

    private function seedUsers(): void
    {
        foreach (TestData::users() as $user) {
            $this->userModel->create();

            foreach ($user as $field => $value) {
                $this->userModel->prop($field, $value);
            }

            $this->userModel->save();
        }
    }

    private function seedUserProfessions(): void
    {
        foreach (TestData::userProfessions() as $profession) {
            $this->userProfessionModel->create();

            foreach ($profession as $field => $value) {
                $this->userProfessionModel->prop($field, $value);
            }

            $this->userProfessionModel->save();
        }
    }

    private function seedEvents(): void
    {
        foreach (TestData::events() as $event) {
            $this->eventModel->create();

            foreach ($event as $field => $value) {
                $this->eventModel->prop($field, $value);
            }

            $this->eventModel->save();
        }
    }

    private function seedUserEvents(): void
    {
        foreach (TestData::userEvents() as $userEvent) {
            $this->userEventModel->create();

            foreach ($userEvent as $field => $value) {
                $this->userEventModel->prop($field, $value);
            }

            $this->userEventModel->save();
        }
    }

    private function seedUserMeetings(): void
    {
        foreach (TestData::userMeetings() as $meeting) {
            $this->userMeetingModel->create();

            foreach ($meeting as $field => $value) {
                $this->userMeetingModel->prop($field, $value);
            }

            $this->userMeetingModel->save();
        }
    }

    private function seedTickets(): void
    {
        foreach (TestData::tickets() as $ticket) {
            $this->ticketModel->create();

            foreach ($ticket as $field => $value) {
                $this->ticketModel->prop($field, $value);
            }

            $this->ticketModel->save();
        }
    }

    private function seedNotes(): void
    {
        foreach (TestData::notes() as $note) {
            $this->noteModel->create();

            foreach ($note as $field => $value) {
                $this->noteModel->prop($field, $value);
            }

            $this->noteModel->save();
        }
    }
}