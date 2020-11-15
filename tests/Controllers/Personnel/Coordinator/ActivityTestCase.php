<?php

namespace Tests\Controllers\Personnel\Coordinator;

use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\Controllers\RecordPreparation\Firm\Program\ {
    ActivityType\RecordOfActivityParticipant,
    Coordinator\RecordOfCoordinatorActivity,
    RecordOfActivity,
    RecordOfActivityType
};

class ActivityTestCase extends CoordinatorTestCase
{
    protected $activityUri;
    /**
     *
     * @var RecordOfActivityType
     */
    protected $activityType;
    /**
     *
     * @var RecordOfCoordinatorActivity
     */
    protected $coordinatorActivity;
    /**
     *
     * @var RecordOfActivityParticipant
     */
    protected $activityParticipant_coordinator;
    /**
     *
     * @var RecordOfActivityParticipant
     */
    protected $activityParticipantOne_Manager;
    /**
     *
     * @var RecordOfActivityParticipant
     */
    protected $activityParticipantTwo_Consultant;
    /**
     *
     * @var RecordOfActivityParticipant
     */
    protected $activityParticipantThree_Participant;


    protected function setUp(): void
    {
        parent::setUp();
        $this->activityUri = $this->coordinatorUri . "/{$this->coordinator->id}/activities";
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        
        $program = $this->coordinator->program;
        
        $this->activityType = new RecordOfActivityType($program, "999");
        $this->connection->table("ActivityType")->insert($this->activityType->toArrayForDbEntry());
        
        $this->activityParticipant_coordinator = new RecordOfActivityParticipant($this->activityType, null, "999");
        $this->activityParticipantOne_Manager = new RecordOfActivityParticipant($this->activityType, null, "998");
        $this->activityParticipantOne_Manager->participantType = ActivityParticipantType::MANAGER;
        $this->activityParticipantTwo_Consultant = new RecordOfActivityParticipant($this->activityType, null, "997");
        $this->activityParticipantTwo_Consultant->participantType = ActivityParticipantType::CONSULTANT;
        $this->activityParticipantThree_Participant = new RecordOfActivityParticipant($this->activityType, null, "996");
        $this->activityParticipantThree_Participant->participantType = ActivityParticipantType::PARTICIPANT;
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipant_coordinator->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipantOne_Manager->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipantTwo_Consultant->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($this->activityParticipantThree_Participant->toArrayForDbEntry());
        
        $activity = new RecordOfActivity($program, $this->activityType, "999");
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
