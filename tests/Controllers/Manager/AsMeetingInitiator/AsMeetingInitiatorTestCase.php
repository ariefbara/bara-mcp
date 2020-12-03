<?php

namespace Tests\Controllers\Manager\AsMeetingInitiator;

use Tests\Controllers\ {
    Manager\ManagerTestCase,
    RecordPreparation\Firm\Manager\RecordOfActivityInvitation,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitee,
    RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant,
    RecordPreparation\Firm\Program\RecordOfActivity,
    RecordPreparation\Firm\Program\RecordOfActivityType,
    RecordPreparation\Firm\RecordOfProgram
};

class AsMeetingInitiatorTestCase extends ManagerTestCase
{
    protected $asMeetingInitiatorUri;
    /**
     *
     * @var RecordOfActivity
     */
    protected $meeting;
    /**
     *
     * @var RecordOfActivityInvitation
     */
    protected $meetingAttendace;
    
    protected function setUp(): void
    {
        parent::setUp();
        $firm = $this->manager->firm;
        
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        
        $program = new RecordOfProgram($firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $activityType = new RecordOfActivityType($program, 999);
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());
        
        $this->meeting = new RecordOfActivity($activityType, 999);
        $this->connection->table("Activity")->insert($this->meeting->toArrayForDbEntry());
        
        $activityParticipant = new RecordOfActivityParticipant($activityType, null, 999);
        $activityParticipant->participantType = "manager";
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());
        
        $attendee = new RecordOfInvitee($this->meeting, $activityParticipant, 999);
        $attendee->anInitiator = true;
        $this->connection->table("Invitee")->insert($attendee->toArrayForDbEntry());
        
        $this->meetingAttendace = new RecordOfActivityInvitation($this->manager, $attendee);
        $this->connection->table("ManagerInvitee")->insert($this->meetingAttendace->toArrayForDbEntry());
        
        $this->asMeetingInitiatorUri = $this->managerUri . "/as-meeting-initiator/{$this->meeting->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
    }
    protected function setInactiveMeetingInitiator()
    {
        $this->connection->table("Invitee")->truncate();
        $this->meetingAttendace->invitee->cancelled = true;
        $this->connection->table("Invitee")->insert($this->meetingAttendace->invitee->toArrayForDbEntry());
    }
}
