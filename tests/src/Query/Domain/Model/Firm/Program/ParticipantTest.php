<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
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
    
    protected $teamParticipant;
    protected $userParticipant;
    protected $clientParticipant;
    protected $client;
    protected $task;
    protected $taskInProgram;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->program->expects($this->any())->method('getId')->willReturn($this->programId);
        $this->participant->program = $this->program;
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
        $this->missionCommentRepository = $this->buildMockOfInterface(MissionCommentRepository::class);
        
        $this->teamParticipant = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        
        $this->client = $this->buildMockOfClass(Client::class);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByParticipant::class);
        $this->taskInProgram = $this->buildMockOfInterface(ITaskInProgramExecutableByParticipant::class);
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
    
    protected function getListOfClientPlusTeamName()
    {
        return $this->participant->getListOfClientPlusTeamName();
    }
    public function test_getListOfClientPlusTeamName_userParticipant_returnUserParticipantName()
    {
        $this->participant->userParticipant = $this->userParticipant;
        $this->userParticipant->expects($this->once())
                ->method('getUserName')
                ->willReturn($userName = 'user name');
        $this->assertEquals([$userName], $this->getListOfClientPlusTeamName());
    }
    public function test_getListOfClientPlusTeamName_clientParticipant_returnClientParticipantName()
    {
        $this->participant->clientParticipant = $this->clientParticipant;
        $this->clientParticipant->expects($this->once())
                ->method('getClientName')
                ->willReturn($clientName = 'client name');
        $this->assertEquals([$clientName], $this->getListOfClientPlusTeamName());
    }
    public function test_getListOfClientPlusTeamName_teamParticipant_returnTeamParticipantListOfActiveMemberPlusTeamName()
    {
        $this->participant->teamParticipant = $this->teamParticipant;
        $this->teamParticipant->expects($this->once())
                ->method('getListOfActiveMemberPlusTeamName');
        $this->getListOfClientPlusTeamName();
    }
    
    protected function correspondWithClient()
    {
        return $this->participant->correspondWithClient($this->client);
    }
    public function test_correspondWithClient_clientParticipant_returnClientEqualsResult()
    {
        $this->participant->clientParticipant = $this->clientParticipant;
        $this->clientParticipant->expects($this->once())
                ->method('clientEquals')
                ->with($this->client);
        $this->correspondWithClient();
    }
    public function test_correspondWithClient_teamParticipant_returnTeamParticipantHasActiveMemberCorrespondWithClientResult()
    {
        $this->participant->teamParticipant = $this->teamParticipant;
        $this->teamParticipant->expects($this->once())
                ->method('hasActiveMemberCorrespondWithClient')
                ->with($this->client);
        $this->correspondWithClient();
    }
    public function test_correspondWithClient_userParticipant_returnFalse()
    {
        $this->participant->userParticipant = $this->userParticipant;
        $this->assertFalse($this->correspondWithClient());
    }
    
    protected function getTeamName()
    {
        return $this->participant->getTeamName();
    }
    public function test_getTeamName_teamParticipant_returnTeamName()
    {
        $this->participant->teamParticipant = $this->teamParticipant;
        $this->teamParticipant->expects($this->once())
                ->method('getTeamName');
        $this->getTeamName();
    }
    public function test_getTeamName_notTeamParticipant_returnNull()
    {
        $this->assertNull($this->getTeamName());
    }
    
    protected function executeTask()
    {
        $this->participant->executeTask($this->task);
    }
    public function test_executeTask_executeTask()
    {
        $this->task->expects($this->once())
                ->method('execute')
                ->with($this->participant->id);
        $this->executeTask();
    }
    public function test_executeTask_inactiveParticipant_403()
    {
        $this->participant->active = false;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeTask();
        }, 'Forbidden', 'forbidden: only active participant can make this request');
    }
    
    protected function executeTaskInProgram()
    {
        $this->participant->executeTaskInProgram($this->taskInProgram);
    }
    public function test_executeTaskInProgram_executeTask()
    {
        $this->taskInProgram->expects($this->once())
                ->method('executeTaskInProgram')
                ->with($this->participant->program->getId());
        $this->executeTaskInProgram();
    }
    public function test_executeTaskInProgram_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->executeTaskInProgram();
        }, 'Forbidden', 'forbidden: only active participant can make this request');
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
