<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;
use Query\Domain\Service\LearningMaterialFinder;
use Tests\TestBase;

class ParticipantTest extends TestBase
{
    protected $participant;
    protected $program, $programId = 'programId';
    protected $learningMaterialFinder, $learningMaterialId = "learningMaterialId";
    protected $page = 1, $pageSize = 25;
    protected $missionCommentRepository, $missionId = 'missionId', $missionCommentId = 'missionCommentId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->program->expects($this->any())->method('getId')->willReturn($this->programId);
        $this->participant->program = $this->program;
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
        $this->missionCommentRepository = $this->buildMockOfInterface(MissionCommentRepository::class);
    }
    protected function assertInactiveParticipant(callable $operation)
    {
        $this->assertRegularExceptionThrowed(
                $operation, 'Forbidden', 'forbidden: only active participant can make this request');
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
    
    protected function executeViewMissionComment()
    {
        $this->participant->viewMissionComment($this->missionCommentRepository, $this->missionCommentId);
    }
    public function test_viewMissionComment_returnRepositoryAMissionCommentInProgramResult()
    {
        $this->missionCommentRepository->expects($this->once())
                ->method('aMissionCommentInProgram')
                ->with($this->programId, $this->missionCommentId);
        $this->executeViewMissionComment();
    }
    public function test_viewMissionComment_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertInactiveParticipant(function (){
            $this->executeViewMissionComment();
        });
    }
    
    protected function executeViewAllMissionComments()
    {
        $this->participant->viewAllMissionComments($this->missionCommentRepository, $this->missionId, $this->page, $this->pageSize);
    }
    public function test_viewAllMissionComments_returnRepositoryAllMissionCommentsBelongsInMission()
    {
        $this->missionCommentRepository->expects($this->once())
                ->method('allMissionCommentsBelongsInMission')
                ->with($this->programId, $this->missionId, $this->page, $this->pageSize);
        $this->executeViewAllMissionComments();
    }
    public function test_viewAllMissionComments_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertInactiveParticipant(function (){
            $this->executeViewAllMissionComments();
        });
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
