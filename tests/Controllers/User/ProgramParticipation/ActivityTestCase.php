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
    /**
     *
     * @var RecordOfActivityParticipant
     */
    protected $activityParticipant_consultant;
    /**
     *
     * @var RecordOfActivityParticipant
     */
    protected $activityParticipantOne_Manager;
    /**
     *
     * @var RecordOfActivityParticipant
     */
    protected $activityParticipantTwo_Coordinator;
    /**
     *
     * @var RecordOfActivityParticipant
     */
    protected $activityParticipantThree_Participant;
    
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
        
        $this->activityParticipant_consultant = new RecordOfActivityParticipant($activityType, null, "999");
        $this->activityParticipant_consultant->participantType = ActivityParticipantType::CONSULTANT;
        $this->activityParticipantOne_Manager= new RecordOfActivityParticipant($activityType, null, "998");
        $this->activityParticipantOne_Manager->participantType = ActivityParticipantType::MANAGER;
        $this->activityParticipantTwo_Coordinator = new RecordOfActivityParticipant($activityType, null, "997");
        $this->activityParticipantTwo_Coordinator->participantType = ActivityParticipantType::COORDINATOR;
        $this->activityParticipantThree_Participant = new RecordOfActivityParticipant($activityType, null, "996");
        $this->activityParticipantThree_Participant->participantType = ActivityParticipantType::PARTICIPANT;
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipant_consultant->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipantOne_Manager->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipantTwo_Coordinator->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipantThree_Participant->toArrayForDbEntry());
        
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
