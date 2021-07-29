<?php

namespace Tests\Controllers\Client\ProgramParticipation\AsMeetingInitiator;

use Tests\Controllers\Client\ProgramParticipationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;

class AsMeetingInitiatorTestCase extends ProgramParticipationTestCase
{
    protected $asMeetingInitiatorUri;
    protected $meetingInitiatorUri;
    
    /**
     *
     * @var RecordOfActivity
     */
    protected $meeting;
    /**
     * 
     * @var RecordOfParticipantInvitee
     */
    protected $participantInvitee;
    
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
        
        $invitee = new RecordOfInvitee($this->meeting, $activityParticipant, 'initiator');
        $invitee->anInitiator = true;
        $this->participantInvitee = new RecordOfParticipantInvitee($participant, $invitee);
        $this->participantInvitee->insert($this->connection);
        
        $this->meetingInitiatorUri = $this->programParticipationUri . "/{$this->programParticipation->id}/meeting-initiator/{$this->participantInvitee->invitee->id}";
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
    protected function setManagerCanBeInvited()
    {
        $activityParticipant = new RecordOfActivityParticipant($this->meeting->activityType, null, 998);
        $activityParticipant->participantType = "manager";
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());
    }
    protected function setCoordinatorCanBeInvited()
    {
        $activityParticipant = new RecordOfActivityParticipant($this->meeting->activityType, null, 998);
        $activityParticipant->participantType = "coordinator";
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());
    }
    protected function setConsultantCanBeInvited()
    {
        $activityParticipant = new RecordOfActivityParticipant($this->meeting->activityType, null, 998);
        $activityParticipant->participantType = "consultant";
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());
    }
    protected function setParticipantCanBeInvited()
    {
        $activityParticipant = new RecordOfActivityParticipant($this->meeting->activityType, null, 998);
        $activityParticipant->participantType = "participant";
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());
    }
}
