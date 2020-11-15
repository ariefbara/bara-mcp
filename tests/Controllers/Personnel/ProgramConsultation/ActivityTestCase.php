<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\Controllers\ {
    Personnel\ProgramConsultation\ProgramConsultationTestCase,
    RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant,
    RecordPreparation\Firm\Program\Consultant\RecordOfConsultantActivity,
    RecordPreparation\Firm\Program\RecordOfActivity,
    RecordPreparation\Firm\Program\RecordOfActivityType
};

class ActivityTestCase extends ProgramConsultationTestCase
{
    protected $activityUri;
    /**
     *
     * @var RecordOfConsultantActivity
     */
    protected $consultantActivity;
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
        $this->activityUri = $this->programConsultationUri . "/activities";
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        
        $program = $this->programConsultation->program;
        
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
        
        $this->consultantActivity = new RecordOfConsultantActivity($this->programConsultation, $activity);
        $this->connection->table("ConsultantActivity")->insert($this->consultantActivity->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
    }
}
