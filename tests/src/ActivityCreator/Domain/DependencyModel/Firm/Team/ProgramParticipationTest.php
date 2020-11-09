<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Team;

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
    
    public function test_belongsToTeam_sameTeam_returnTrue()
    {
        $this->assertTrue($this->programParticipation->belongsToTeam($this->programParticipation->teamId));
    }
    public function test_belongsToTeam_differentTeam_returnFalse()
    {
        $this->assertFalse($this->programParticipation->belongsToTeam("differentId"));
    }
    
    public function test_initiateActivity_returnParticipantInitiateActivityResult()
    {
        $this->participant->expects($this->once())
                ->method("initiateActivity")
                ->with($this->activityId, $this->activityType, $this->activityDataProvider);
        $this->programParticipation->initiateActivity($this->activityId, $this->activityType, $this->activityDataProvider);
    }
}

class TestableProgramParticipation extends ProgramParticipation
{
    public $teamId = "teamId";
    public $id;
    public $participant;
    
    function __construct()
    {
        parent::__construct();
    }
}
