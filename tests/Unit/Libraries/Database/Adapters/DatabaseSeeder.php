<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters;

use Quantum\Tests\_root\shared\Models\TestUserProfessionModel;
use Quantum\Tests\_root\shared\Models\TestUserMeetingModel;
use Quantum\Tests\_root\shared\Models\TestUserEventModel;
use Quantum\Tests\_root\shared\Models\TestProfileModel;
use Quantum\Tests\_root\shared\Models\TestTicketModel;
use Quantum\Tests\_root\shared\Models\TestEventModel;
use Quantum\Tests\_root\shared\Models\TestNotesModel;
use Quantum\Tests\_root\shared\Models\TestUserModel;

class DatabaseSeeder
{
    private $userModel;
    private $profileModel;
    private $userProfessionModel;
    private $eventModel;
    private $userEventModel;
    private $userMeetingModel;
    private $ticketModel;
    private $noteModel;

    public function __construct()
    {
        $this->userModel = model(TestUserModel::class);
        $this->profileModel = model(TestProfileModel::class);
        $this->userProfessionModel = model(TestUserProfessionModel::class);
        $this->eventModel = model(TestEventModel::class);
        $this->userEventModel = model(TestUserEventModel::class);
        $this->userMeetingModel = model(TestUserMeetingModel::class);
        $this->ticketModel = model(TestTicketModel::class);
        $this->noteModel = model(TestNotesModel::class);
    }

    public function seed(): void
    {
        $this->seedUsers();
        $this->seedProfiles();
        $this->seedUserProfessions();
        $this->seedEvents();
        $this->seedUserEvents();
        $this->seedUserMeetings();
        $this->seedTickets();
        $this->seedNotes();
    }

    private function seedUsers(): void
    {

        foreach (TestData::users() as $userData) {
            $this->userModel->create();
            $this->userModel->fillObjectProps($userData);
            $this->userModel->save();
        }
    }

    private function seedProfiles(): void
    {
        foreach (TestData::profiles() as $profileData) {
            $this->profileModel->create();
            $this->profileModel->fillObjectProps($profileData);
            $this->profileModel->save();
        }
    }

    private function seedUserProfessions(): void
    {
        foreach (TestData::userProfessions() as $professionData) {
            $this->userProfessionModel->create();
            $this->userProfessionModel->fillObjectProps($professionData);
            $this->userProfessionModel->save();
        }
    }

    private function seedEvents(): void
    {
        foreach (TestData::events() as $eventData) {
            $this->eventModel->create();
            $this->eventModel->fillObjectProps($eventData);
            $this->eventModel->save();
        }
    }

    private function seedUserEvents(): void
    {
        foreach (TestData::userEvents() as $userEventData) {
            $this->userEventModel->create();
            $this->userEventModel->fillObjectProps($userEventData);
            $this->userEventModel->save();
        }
    }

    private function seedUserMeetings(): void
    {
        foreach (TestData::userMeetings() as $meetingData) {
            $this->userMeetingModel->create();
            $this->userMeetingModel->fillObjectProps($meetingData);
            $this->userMeetingModel->save();
        }
    }

    private function seedTickets(): void
    {
        foreach (TestData::tickets() as $ticketData) {
            $this->ticketModel->create();
            $this->ticketModel->fillObjectProps($ticketData);
            $this->ticketModel->save();
        }
    }

    private function seedNotes(): void
    {
        foreach (TestData::notes() as $noteData) {
            $this->noteModel->create();
            $this->noteModel->fillObjectProps($noteData);
            $this->noteModel->save();
        }
    }
}
