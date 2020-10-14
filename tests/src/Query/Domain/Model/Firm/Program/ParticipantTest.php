<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\ {
    Model\Firm\Program,
    Service\LearningMaterialFinder
};
use Tests\TestBase;

class ParticipantTest extends TestBase
{
    protected $participant;
    protected $learningMaterialFinder, $learningMaterialId = "learningMaterialId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant();
        $this->participant->program = $this->buildMockOfClass(Program::class);
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
    }
    
    
    protected function executeViewLearningMaterial()
    {
        $this->participant->viewLearningMaterial($this->learningMaterialFinder, $this->learningMaterialId);
    }
    public function test_viewLearningMaterial_returnLearningMaterialFinderExecuteResult()
    {
        $this->learningMaterialFinder->expects($this->once())
                ->method("execute")
                ->with($this->participant->program, $this->learningMaterialId);
        $this->executeViewLearningMaterial();
    }
    public function test_viewLearningMaterial_inactiveParticipant_forbiddenError()
    {
        $this->participant->active = false;
        $operation = function (){
            $this->executeViewLearningMaterial();
        };
        $errorDetail = "forbidden: only active participant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}

class TestableParticipant extends Participant
{
    public $program;
    public $id = "participantId";
    public $enrolledTime;
    public $active = true;
    public $note;
    public $clientParticipant;
    public $userParticipant;
    public $teamParticipant;
    
    function __construct()
    {
        parent::__construct();
    }
}
