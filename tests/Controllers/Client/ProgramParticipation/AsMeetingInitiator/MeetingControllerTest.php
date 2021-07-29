<?php

namespace Tests\Controllers\Client\ProgramParticipation\AsMeetingInitiator;

use DateTimeImmutable;
use Tests\Controllers\MailChecker;
use Tests\Controllers\NotificationChecker;

class MeetingControllerTest extends AsMeetingInitiatorTestCase
{
    protected $updateInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->updateInput = [
            "name" => "new name",
            "description" => "new description",
            "startTime" => (new DateTimeImmutable("+24 hours"))->format("Y-m-d H:i:s"),
            "endTime" => (new DateTimeImmutable("+26 hours"))->format("Y-m-d H:i:s"),
            "location" => "new location",
            "note" => "new note",
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_update_200()
    {
        $response = [
            "id" => $this->meeting->id,
            "name" => $this->updateInput["name"],
            "description" => $this->updateInput["description"],
            "startTime" => $this->updateInput["startTime"],
            "endTime" => $this->updateInput["endTime"],
            "location" => $this->updateInput["location"],
            "note" => $this->updateInput["note"],
        ];
        
        $uri = $this->meetingInitiatorUri . "/update-meeting";
        $this->patch($uri, $this->updateInput, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $meetingEntry = [
            "id" => $this->meeting->id,
            "name" => $this->updateInput["name"],
            "description" => $this->updateInput["description"],
            "startDateTime" => $this->updateInput["startTime"],
            "endDateTime" => $this->updateInput["endTime"],
            "location" => $this->updateInput["location"],
            "note" => $this->updateInput["note"],
        ];
        $this->seeInDatabase("Activity", $meetingEntry);
    }
    public function test_update_sendMailAndNotification()
    {
        $uri = $this->meetingInitiatorUri . "/update-meeting";
        $this->patch($uri, $this->updateInput, $this->programParticipation->client->token);
        
        (new MailChecker())->checkMailExist($subject = "Meeting Schedule Changed", $this->client->email);
        (new NotificationChecker())
                ->checkNotificationExist($message = "meeting scheduled changed")
                ->checkClientNotificationExist($this->client->id);
    }
}
