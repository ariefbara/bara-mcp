<?php

namespace Tests\Controllers\Manager;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\ {
    Program\ActivityType\RecordOfActivityParticipant,
    Program\RecordOfActivityType,
    RecordOfProgram
};

class MeetingControllerTest extends ManagerTestCase
{
    protected $meetingUri;
    protected $meetingType;
    protected $attendeeSetup;
    
    protected $initiateInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingUri = $this->managerUri . "/meetings";
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("Mail")->truncate();
        $this->connection->table("MailRecipient")->truncate();
        $this->connection->table("IcalAttachment")->truncate();
        
        $firm = $this->manager->firm;
        
        $program = new RecordOfProgram($firm, 0);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $this->meetingType = new RecordOfActivityType($program, 0);
        $this->connection->table("ActivityType")->insert($this->meetingType->toArrayForDbEntry());
        
        $this->attendeeSetup = new RecordOfActivityParticipant($this->meetingType, null, 0);
        $this->attendeeSetup->participantType = "manager";
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
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
//        $this->connection->table("Mail")->truncate();
//        $this->connection->table("MailRecipient")->truncate();
//        $this->connection->table("IcalAttachment")->truncate();
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
        
        $this->post($this->meetingUri, $this->initiateInput, $this->manager->token);
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
//            "createdTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("Activity", $meetingEntry);
    }
    public function test_initiate_aggregateInitiatorAsAttendee()
    {
        $this->post($this->meetingUri, $this->initiateInput, $this->manager->token);
//                ->seeStatusCode(201);
        
        $attendeeEntry = [
            "ActivityParticipant_id" => $this->attendeeSetup->id,
            "anInitiator" => true,
            "willAttend" => true,
            "attended" => false,
            "cancelled" => false,
        ];
        $this->seeInDatabase("Invitee", $attendeeEntry);
        
        $managerAttendeeEntry = [
            "Manager_id" => $this->manager->id,
        ];
        $this->seeInDatabase("ManagerInvitee", $managerAttendeeEntry);
    }
    
}
