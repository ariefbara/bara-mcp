<?php

namespace Query\Domain\Model\Firm\Team;

use Query\Domain\ {
    Model\Firm\Program\Participant,
    Service\LearningMaterialFinder
};
use Tests\TestBase;

class TeamProgramParticipationTest extends TestBase
{
    protected $teamProgramParticipation;
    protected $programParticipation;
    protected $learningMaterialFinder, $learningMaterialId = "learningMaterialId";


    protected function setUp(): void
    {
        parent::setUp();
        $this->teamProgramParticipation = new TestableTeamProgramParticipation();
        
        $this->programParticipation = $this->buildMockOfClass(Participant::class);
        $this->teamProgramParticipation->programParticipation = $this->programParticipation;
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
    }
    
    public function test_viewLearningMaterial_returnProgramParticipationViewLearningMaterialResult()
    {
        $this->programParticipation->expects($this->once())
                ->method("viewLearningMaterial")
                ->with($this->learningMaterialFinder, $this->learningMaterialId);
        $this->teamProgramParticipation->viewLearningMaterial($this->learningMaterialFinder, $this->learningMaterialId);
    }
    
    public function test_pullRecordedEvents_returnProgramParticipationsPullRecordedEventsResult()
    {
        $this->programParticipation->expects($this->once())
                ->method("pullRecordedEvents");
        $this->teamProgramParticipation->pullRecordedEvents();
    }
}

class TestableTeamProgramParticipation extends TeamProgramParticipation
{
    public $team;
    public $id;
    public $programParticipation;
    
    function __construct()
    {
        parent::__construct();
    }
}
