<?php

namespace Query\Domain\Model\User;

use Query\Domain\ {
    Model\Firm\Program\Participant,
    Service\LearningMaterialFinder
};
use Tests\TestBase;

class UserParticipantTest extends TestBase
{
    protected $userParticipant;
    protected $participant;
    protected $learningMaterialFinder;
    protected $learningMaterialId = "learningMaterialId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipant = new TestableUserParticipant();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->userParticipant->participant = $this->participant;
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
    }
    
    public function test_viewLearningMaterial_returnParticipantViewLearningMaterialResult()
    {
        $this->participant->expects($this->once())
                ->method("viewLearningMaterial")
                ->with($this->learningMaterialFinder, $this->learningMaterialId);
        $this->userParticipant->viewLearningMaterial($this->learningMaterialFinder, $this->learningMaterialId);
    }
    
    public function test_pullRecordedEvents_returnParticipantsPullRecordedEventsResult()
    {
        $this->participant->expects($this->once())
                ->method("pullRecordedEvents");
        $this->userParticipant->pullRecordedEvents();
    }
}

class TestableUserParticipant extends UserParticipant
{
    public $user;
    public $id;
    public $participant;
    
    function __construct()
    {
        parent::__construct();
    }
}
