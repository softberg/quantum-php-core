<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters;

class TestData
{
    public static function users(): array
    {
        return [
            ['email' => 'john@test.com', 'password' => 'hashed1'],
            ['email' => 'jane@test.com', 'password' => 'hashed2'],
        ];
    }

    public static function profiles(): array
    {
        return [
            ['user_id' => 1, 'firstname' => 'John', 'lastname' => 'Doe', 'age' => 45, 'country' => 'Ireland', 'created_at' => date('Y-m-d H:i:s')],
            ['user_id' => 2, 'firstname' => 'Jane', 'lastname' => 'Du', 'age' => 35, 'country' => 'England', 'created_at' => date('Y-m-d H:i:s')],
        ];
    }

    public static function events(): array
    {
        return [
            ['title' => 'Dance', 'country' => 'New Zealand', 'started_at' => '2019-01-04 20:28:33'],
            ['title' => 'Music', 'country' => 'England', 'started_at' => '2019-09-14 10:15:12'],
            ['title' => 'Design', 'country' => 'Ireland', 'started_at' => '2020-02-14 10:15:12'],
            ['title' => 'Music', 'country' => 'Ireland', 'started_at' => '2020-12-14 10:15:12'],
            ['title' => 'Film', 'country' => 'Ireland', 'started_at' => '2040-02-14 10:15:12'],
            ['title' => 'Art', 'country' => 'Island', 'started_at' => null],
            ['title' => 'Music', 'country' => 'Island', 'started_at' => null],
        ];
    }

    public static function userEvents(): array
    {
        return [
            ['user_id' => 1, 'event_id' => 1, 'confirmed' => 'Yes', 'created_at' => '2020-01-04 20:28:33'],
            ['user_id' => 1, 'event_id' => 2, 'confirmed' => 'No', 'created_at' => '2020-02-19 05:15:12'],
            ['user_id' => 1, 'event_id' => 4, 'confirmed' => 'No', 'created_at' => '2020-02-22 11:15:15'],
            ['user_id' => 2, 'event_id' => 2, 'confirmed' => 'Yes', 'created_at' => '2020-03-10 02:17:12'],
            ['user_id' => 2, 'event_id' => 3, 'confirmed' => 'No', 'created_at' => '2020-04-17 12:25:18'],
            ['user_id' => 2, 'event_id' => 5, 'confirmed' => 'No', 'created_at' => '2020-04-15 11:10:12'],
            ['user_id' => 100, 'event_id' => 200, 'confirmed' => 'Yes', 'created_at' => '2020-04-15 11:10:12'],
            ['user_id' => 110, 'event_id' => 220, 'confirmed' => 'No', 'created_at' => '2020-04-15 11:10:12'],
        ];
    }

    public static function userProfessions(): array
    {
        return [
            ['user_id' => 1, 'title' => 'Writer'],
            ['user_id' => 2, 'title' => 'Singer'],
        ];
    }

    public static function userMeetings(): array
    {
        return [
            ['user_id' => 1, 'title' => 'Business planning', 'start_date' => '2021-11-01 11:00:00'],
            ['user_id' => 1, 'title' => 'Business management', 'start_date' => '2021-11-05 11:00:00'],
            ['user_id' => 2, 'title' => 'Marketing', 'start_date' => '2021-11-10 13:00:00'],
        ];
    }

    public static function tickets(): array
    {
        return [
            ['meeting_id' => 1, 'type' => 'regular', 'number' => 'R1245'],
            ['meeting_id' => 1, 'type' => 'regular', 'number' => 'R4563'],
            ['meeting_id' => 1, 'type' => 'vip', 'number' => 'V4563'],
            ['meeting_id' => 2, 'type' => 'vip', 'number' => 'V7854'],
            ['meeting_id' => 3, 'type' => 'vip', 'number' => 'V7410'],
            ['meeting_id' => 3, 'type' => 'vip', 'number' => 'RT2233'],
        ];
    }

    public static function notes(): array
    {
        return [
            ['ticket_id' => 1, 'note' => 'note one'],
            ['ticket_id' => 1, 'note' => 'note two'],
            ['ticket_id' => 2, 'note' => 'note three'],
            ['ticket_id' => 3, 'note' => 'note four'],
            ['ticket_id' => 4, 'note' => 'note five'],
            ['ticket_id' => 4, 'note' => 'note six'],
            ['ticket_id' => 6, 'note' => 'note seven'],
        ];
    }
}