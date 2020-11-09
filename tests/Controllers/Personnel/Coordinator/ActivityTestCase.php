<?php

namespace Tests\Controllers\Personnel\Coordinator;

use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\Controllers\RecordPreparation\Firm\ {
    Program\ActivityType\RecordOfActivityParticipant,
    Program\Coordinator\RecordOfCoordinatorActivity,
    Program\RecordOfActivity,
    Program\RecordOfActivityType,
    RecordOfProgram
};

class ActivityTestCase extends CoordinatorTestCase
{
    protected $activityUri;
    /**
     *
     * @var RecordOfCoordinatorActivity
     */
    protected $coordinatorActivity;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->activityUri = $this->coordinatorUri . "/{$this->coordinator->id}/activities";
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        
        $program = $this->coordinator->program;
        
        $activityType = new RecordOfActivityType($program, "999");
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());
        
        $activityParticipant = new RecordOfActivityParticipant($activityType, null, "999");
        $activityParticipantOne_Manager= new RecordOfActivityParticipant($activityType, null, "998");
        $activityParticipantOne_Manager->participantType = ActivityParticipantType::MANAGER;
        $activityParticipantTwo_Consultant = new RecordOfActivityParticipant($activityType, null, "997");
        $activityParticipantTwo_Consultant->participantType = ActivityParticipantType::CONSULTANT;
        $activityParticipantThree_Participant = new RecordOfActivityParticipant($activityType, null, "996");
        $activityParticipantThree_Participant->participantType = ActivityParticipantType::PARTICIPANT;
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipantOne_Manager->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipantTwo_Consultant->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipantThree_Participant->toArrayForDbEntry());
        
        $activity = new RecordOfActivity($program, $activityType, "999");
        $this->connection->table("Activity")->insert($activity->toArrayForDbEntry());
        
        $this->coordinatorActivity = new RecordOfCoordinatorActivity($this->coordinator, $activity);
        $this->connection->table("CoordinatorActivity")->insert($this->coordinatorActivity->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
    }
}
