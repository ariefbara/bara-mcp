<?php

namespace Tests\Controllers\Personnel\AsCoordinatorMeetingInitiator;

class MeetingControllerTest extends AsMeetingInitiatorTestCase
{
    protected $updateInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->updateInput = [
            "name" => "new name",
            "description" => "new description",
            "startTime" => (new \DateTimeImmutable("+24 hours"))->format("Y-m-d H:i:s"),
            "endTime" => (new \DateTimeImmutable("+26 hours"))->format("Y-m-d H:i:s"),
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
        
        $uri = $this->asMeetingInitiatorUri . "/update-meeting";
        $this->patch($uri, $this->updateInput, $this->personnel->token);
        
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
}
