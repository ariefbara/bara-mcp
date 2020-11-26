<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation\AsMeetingInitiator;

use Tests\Controllers\ {
    Client\AsTeamMember\ProgramParticipationTestCase,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitee,
    RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant,
    RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation,
    RecordPreparation\Firm\Program\RecordOfActivity,
    RecordPreparation\Firm\Program\RecordOfActivityType
};

class AsMeetingInitiatorTestCase extends ProgramParticipationTestCase
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
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
        
        $activityType = new RecordOfActivityType($program, 999);
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());
        
        $this->meeting = new RecordOfActivity($activityType, 999);
        $this->connection->table("Activity")->insert($this->meeting->toArrayForDbEntry());
        
        $activityParticipant = new RecordOfActivityParticipant($activityType, null, 999);
        $activityParticipant->participantType = "participant";
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());
        
        $attendee = new RecordOfInvitee($this->meeting, $activityParticipant, 999);
        $attendee->anInitiator = true;
        $this->connection->table("Invitee")->insert($attendee->toArrayForDbEntry());
        
        $this->meetingAttendace = new RecordOfActivityInvitation($participant, $attendee);
        $this->connection->table("ParticipantInvitee")->insert($this->meetingAttendace->toArrayForDbEntry());
        
        $this->asMeetingInitiatorUri = $this->programParticipationUri . "/{$this->programParticipation->id}/as-meeting-initiator/{$this->meeting->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
    }
}
