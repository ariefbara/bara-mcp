<?php

namespace Tests\Controllers\Manager;

use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\Controllers\RecordPreparation\Firm\ {
    Manager\RecordOfManagerActivity,
    Program\ActivityType\RecordOfActivityParticipant,
    Program\RecordOfActivity,
    Program\RecordOfActivityType,
    RecordOfProgram
};

class ActivityTestCase extends ManagerTestCase
{
    protected $activityUri;
    /**
     *
     * @var RecordOfManagerActivity
     */
    protected $managerActivity;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->activityUri = $this->managerUri . "/activities";
        
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        
        $program = new RecordOfProgram($this->firm, "999");
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $activityType = new RecordOfActivityType($program, "999");
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());
        
        $activityParticipant = new RecordOfActivityParticipant($activityType, null, "999");
        $activityParticipantOne_Manager = new RecordOfActivityParticipant($activityType, null, "998");
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
        
        $this->managerActivity = new RecordOfManagerActivity($this->manager, $activity);
        $this->connection->table("ManagerActivity")->insert($this->managerActivity->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
    }
}
