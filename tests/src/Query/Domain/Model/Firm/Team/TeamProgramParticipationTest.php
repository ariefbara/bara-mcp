<?php

namespace Query\Domain\Model\Firm\Team;

use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;
use Query\Domain\Service\LearningMaterialFinder;
use Query\Domain\Task\Participant\ParticipantQueryTask;
use Tests\TestBase;

class TeamProgramParticipationTest extends TestBase
{
    protected $teamProgramParticipation;
    protected $team;
    protected $programParticipation;
    protected $learningMaterialFinder, $learningMaterialId = "learningMaterialId";
    protected $page = 1, $pageSize = 25;
    protected $missionCommentRepository, $missionId = 'missionId', $missionCommentId = 'missionCommentId';
    
    protected $client;
    protected $task;
    protected $programTask;
    //
    protected $participantTask, $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamProgramParticipation = new TestableTeamProgramParticipation();
        $this->team = $this->buildMockOfClass(Team::class);
        $this->teamProgramParticipation->team = $this->team;
        
        $this->programParticipation = $this->buildMockOfClass(Participant::class);
        $this->teamProgramParticipation->programParticipation = $this->programParticipation;
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
        $this->missionCommentRepository = $this->buildMockOfInterface(MissionCommentRepository::class);
        
        $this->client = $this->buildMockOfClass(Client::class);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByParticipant::class);
        $this->programTask = $this->buildMockOfClass(ITaskInProgramExecutableByParticipant::class);
        //
        $this->participantTask = $this->buildMockOfInterface(ParticipantQueryTask::class);
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
    
    public function test_viewMissionComment_returnParticipantsViewMissionCommentResult()
    {
        $this->programParticipation->expects($this->once())
                ->method('viewMissionComment')
                ->with($this->missionCommentRepository, $this->missionCommentId);
        $this->teamProgramParticipation->viewMissionComment($this->missionCommentRepository, $this->missionCommentId);
    }
    public function test_viewAllMissionComments_returnParticpantsViewAllMissionCommentsResult()
    {
        $this->programParticipation->expects($this->once())
                ->method('viewAllMissionComments')
                ->with($this->missionCommentRepository, $this->missionId, $this->page, $this->pageSize);
        $this->teamProgramParticipation->viewAllMissionComments($this->missionCommentRepository, $this->missionId, $this->page, $this->pageSize);
    }
    
    public function test_hasActiveMemberCorrespondWithClient_returnTeamActiveMemberCheckingStatus()
    {
        $this->team->expects($this->once())
                ->method('hasActiveMemberCorrespondWithClient')
                ->with($this->client);
        $this->teamProgramParticipation->hasActiveMemberCorrespondWithClient($this->client);
    }
    
    public function test_getListOfActiveMemberPlusTeamName_returnTeamListOfActiveMemberPlusTeamName()
    {
        $this->team->expects($this->once())
                ->method('getListOfActiveMemberPlusTeamName');
        $this->teamProgramParticipation->getListOfActiveMemberPlusTeamName();
    }
    
    public function test_executeTask_participantExecuteTask()
    {
        $this->programParticipation->expects($this->once())
                ->method('executeTask')
                ->with($this->task);
        $this->teamProgramParticipation->executeTask($this->task);
    }
    
    protected function executeTaskInProgram()
    {
        $this->teamProgramParticipation->executeTaskInProgram($this->programTask);
    }
    public function test_executeTaskInProgram_forwardToParticipant()
    {
        $this->programParticipation->expects($this->once())
                ->method('executeTaskInProgram')
                ->with($this->programTask);
        $this->executeTaskInProgram();
    }
   
    //
    protected function executeQueryTask()
    {
        $this->teamProgramParticipation->executeQueryTask($this->participantTask, $this->payload);
    }
    public function test_executeQueryTask_participantExecuteTask()
    {
        $this->programParticipation->expects($this->once())
                ->method('executeQueryTask')
                ->with($this->participantTask, $this->payload);
        $this->executeQueryTask();
    }
    
    //
    protected function assertBelongsToTeam()
    {
        $this->teamProgramParticipation->assertBelongsToTeam($this->team);
    }
    public function test_assertBelongsToTeam_differentTeam_forbidden()
    {
        $this->teamProgramParticipation->team = $this->buildMockOfClass(Team::class);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertBelongsToTeam();
        }, 'Forbidden', 'team participant not belongs to team');
    }
    public function test_assertBelongsToTeam_sameTeam_void()
    {
        $this->assertBelongsToTeam();
        $this->markAsSuccess();
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
