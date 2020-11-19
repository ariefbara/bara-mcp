<?php

namespace Tests\Controllers\Personnel\AsMeetingInitiator;

use Tests\Controllers\ {
    Personnel\PersonnelTestCase,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitee,
    RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant,
    RecordPreparation\Firm\Program\Coordinator\RecordOfActivityInvitation,
    RecordPreparation\Firm\Program\RecordOfActivity,
    RecordPreparation\Firm\Program\RecordOfActivityType,
    RecordPreparation\Firm\Program\RecordOfCoordinator,
    RecordPreparation\Firm\RecordOfProgram
};

class AsMeetingInitiatorTestCase extends PersonnelTestCase
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
        $firm = $this->personnel->firm;
        
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        
        $program = new RecordOfProgram($firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $activityType = new RecordOfActivityType($program, 999);
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());
        
        $this->meeting = new RecordOfActivity($activityType, 999);
        $this->connection->table("Activity")->insert($this->meeting->toArrayForDbEntry());
        
        $activityParticipant = new RecordOfActivityParticipant($activityType, null, 999);
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());
        
        $attendee = new RecordOfInvitee($this->meeting, $activityParticipant, 999);
        $attendee->anInitiator = true;
        $this->connection->table("Invitee")->insert($attendee->toArrayForDbEntry());
        
        $coordinator = new RecordOfCoordinator($program, $this->personnel, 999);
        $this->connection->table("Coordinator")->insert($coordinator->toArrayForDbEntry());
        
        $this->meetingAttendace = new RecordOfActivityInvitation($coordinator, $attendee);
        $this->connection->table("CoordinatorInvitee")->insert($this->meetingAttendace->toArrayForDbEntry());
        
        $this->asMeetingInitiatorUri = $this->personnelUri . "/as-meeting-initiator/{$this->meeting->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
    }
}
