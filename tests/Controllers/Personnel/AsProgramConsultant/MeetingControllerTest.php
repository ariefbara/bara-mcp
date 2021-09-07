<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Program\ {
    ActivityType\RecordOfActivityParticipant,
    RecordOfActivityType
};

class MeetingControllerTest extends AsProgramConsultantTestCase
{
    protected $meetingUri;
    protected $meetingType;
    protected $attendeeSetup;
    
    protected $initiateInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingUri = $this->asProgramConsultantUri . "/meetings";
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        
        $program = $this->consultant->program;
        
        $this->meetingType = new RecordOfActivityType($program, 0);
        $this->connection->table("ActivityType")->insert($this->meetingType->toArrayForDbEntry());
        
        $this->attendeeSetup = new RecordOfActivityParticipant($this->meetingType, null, 0);
        $this->attendeeSetup->participantType = "consultant";
        $this->connection->table("ActivityParticipant")->insert($this->attendeeSetup->toArrayForDbEntry());
        
        $this->initiateInput = [
            "meetingTypeId" => $this->meetingType->id,
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
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
    }
    
    public function test_initiate_201()
    {
        $response = [
            "name" => $this->initiateInput["name"],
            "description" => $this->initiateInput["description"],
            "startTime" => $this->initiateInput["startTime"],
            "endTime" => $this->initiateInput["endTime"],
            "location" => $this->initiateInput["location"],
            "note" => $this->initiateInput["note"],
            "cancelled" => false,
            "createdTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        
        $this->post($this->meetingUri, $this->initiateInput, $this->consultant->personnel->token);
//                ->seeJsonContains($response)
//                ->seeStatusCode(201);
        
        $meetingEntry = [
            "ActivityType_id" => $this->initiateInput["meetingTypeId"],
            "name" => $this->initiateInput["name"],
            "description" => $this->initiateInput["description"],
            "startDateTime" => $this->initiateInput["startTime"],
            "endDateTime" => $this->initiateInput["endTime"],
            "location" => $this->initiateInput["location"],
            "note" => $this->initiateInput["note"],
            "cancelled" => false,
        ];
        $this->seeInDatabase("Activity", $meetingEntry);
    }
    public function test_initiate_aggregateInitiatorAsAttendee()
    {
        $this->post($this->meetingUri, $this->initiateInput, $this->consultant->personnel->token);
//                ->seeStatusCode(201);
        
        $attendeeEntry = [
            "ActivityParticipant_id" => $this->attendeeSetup->id,
            "anInitiator" => true,
            "willAttend" => true,
            "attended" => false,
            "cancelled" => false,
        ];
        $this->seeInDatabase("Invitee", $attendeeEntry);
        
        $consultantAttendeeEntry = [
            "Consultant_id" => $this->consultant->id,
        ];
        $this->seeInDatabase("ConsultantInvitee", $consultantAttendeeEntry);
    }
    
}
