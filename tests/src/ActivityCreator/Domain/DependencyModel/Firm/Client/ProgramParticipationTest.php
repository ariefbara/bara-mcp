<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Client;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\ActivityType,
    DependencyModel\Firm\Program\Participant,
    service\ActivityDataProvider
};
use Tests\TestBase;

class ProgramParticipationTest extends TestBase
{
    protected $participant;
    protected $programParticipation;
    protected $activityId = "activityId", $activityType, $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipation = new TestableProgramParticipation();
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->programParticipation->participant = $this->participant;
        
        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }
    
    public function test_initiateActivity_returnParticipatInitiateActivityResult()
    {
        $this->participant->expects($this->once())
                ->method("initiateActivity")
                ->with($this->activityId, $this->activityType, $this->activityDataProvider);
        $this->programParticipation->initiateActivity($this->activityId, $this->activityType, $this->activityDataProvider);
    }
}

class TestableProgramParticipation extends ProgramParticipation
{
    public $client;
    public $id;
    public $participant;
    
    function __construct()
    {
        ;
    }
}
