<?php

namespace Tests\Controllers\Personnel\AsConsultantMeetingInitiator;

use Tests\Controllers\Personnel\PersonnelTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfActivityInvitation;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation as RecordOfActivityInvitation2;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

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
     * @var RecordOfActivityInvitation2
     */
    protected $meetingAttendace;
    /**
     * 
     * @var RecordOfConsultant
     */
    protected $consultant;

    protected function setUp(): void
    {
        parent::setUp();
        $firm = $this->personnel->firm;
        
        $this->connection->table("Program")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        
        $program = new RecordOfProgram($firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $this->consultant = new RecordOfConsultant($program, $this->personnel, 'author');
        $this->connection->table("Consultant")->insert($this->consultant->toArrayForDbEntry());
        
        $activityType = new RecordOfActivityType($program, 999);
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());
        
        $this->meeting = new RecordOfActivity($activityType, 999);
        $this->connection->table("Activity")->insert($this->meeting->toArrayForDbEntry());
        
        $activityParticipant = new RecordOfActivityParticipant($activityType, null, 999);
        $activityParticipant->participantType = "coordinator";
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());
        
        $attendee = new RecordOfInvitee($this->meeting, $activityParticipant, 999);
        $attendee->anInitiator = true;
        $this->connection->table("Invitee")->insert($attendee->toArrayForDbEntry());
        
        $this->meetingAttendace = new RecordOfActivityInvitation($this->consultant, $attendee);
        $this->connection->table("ConsultantInvitee")->insert($this->meetingAttendace->toArrayForDbEntry());
        
        $this->asMeetingInitiatorUri = $this->personnelUri . "/as-consultant-meeting-initiator/{$this->meeting->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
    }
    protected function setInactiveMeetingInitiator()
    {
        $this->connection->table("Invitee")->truncate();
        $this->meetingAttendace->invitee->cancelled = true;
        $this->connection->table("Invitee")->insert($this->meetingAttendace->invitee->toArrayForDbEntry());
    }
}
