<?php

namespace Tests\Controllers\User\ProgramParticipation;

use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\Controllers\ {
    RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant,
    RecordPreparation\Firm\Program\Participant\RecordOfParticipantActivity,
    RecordPreparation\Firm\Program\RecordOfActivity,
    RecordPreparation\Firm\Program\RecordOfActivityType,
    User\ProgramParticipationTestCase
};

class ActivityTestCase extends ProgramParticipationTestCase
{
    protected $activityUri;
    /**
     *
     * @var RecordOfParticipantActivity
     */
    protected $participantActivity;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->activityUri = $this->programParticipationUri . "/{$this->programParticipation->id}/activities";
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        
        $program = $this->programParticipation->participant->program;
        
        $activityType = new RecordOfActivityType($program, "999");
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());
        
        $activityParticipant = new RecordOfActivityParticipant($activityType, null, "999");
        $activityParticipant->participantType = ActivityParticipantType::CONSULTANT;
        $activityParticipantOne_Manager= new RecordOfActivityParticipant($activityType, null, "998");
        $activityParticipantOne_Manager->participantType = ActivityParticipantType::MANAGER;
        $activityParticipantTwo_Coordinator = new RecordOfActivityParticipant($activityType, null, "997");
        $activityParticipantTwo_Coordinator->participantType = ActivityParticipantType::COORDINATOR;
        $activityParticipantThree_Participant = new RecordOfActivityParticipant($activityType, null, "996");
        $activityParticipantThree_Participant->participantType = ActivityParticipantType::PARTICIPANT;
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipantOne_Manager->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipantTwo_Coordinator->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipantThree_Participant->toArrayForDbEntry());
        
        $activity = new RecordOfActivity($program, $activityType, "999");
        $this->connection->table("Activity")->insert($activity->toArrayForDbEntry());
        
        $this->participantActivity = new RecordOfParticipantActivity($this->programParticipation->participant, $activity);
        $this->connection->table("ParticipantActivity")->insert($this->participantActivity->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
    }
}
