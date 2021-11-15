<?php

namespace Query\Domain\Model\Firm\Client;

use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;
use Query\Domain\Service\LearningMaterialFinder;
use Tests\TestBase;

class ClientParticipantTest extends TestBase
{
    protected $clientParticipant;
    protected $client;
    protected $participant;
    protected $learningMaterialFinder;
    protected $learningMaterialId = "learningMaterialId";
    protected $page = 1, $pageSize = 25;
    protected $missionCommentRepository, $missionId = 'missionId', $missionCommentId = 'missionCommentId';
    protected $task;
    protected $taskInProgram;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipant = new TestableClientParticipant();
        
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientParticipant->client = $this->client;
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->clientParticipant->participant = $this->participant;
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
        $this->missionCommentRepository = $this->buildMockOfInterface(MissionCommentRepository::class);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByParticipant::class);
        $this->taskInProgram = $this->buildMockOfInterface(ITaskInProgramExecutableByParticipant::class);
    }
    
    public function test_viewLearningMaterial_returnParticipantsViewLearningMaterialResult()
    {
        $this->participant->expects($this->once())
                ->method("viewLearningMaterial")
                ->with($this->learningMaterialFinder, $this->learningMaterialId);
        $this->clientParticipant->viewLearningMaterial($this->learningMaterialFinder, $this->learningMaterialId);
    }
    
    public function test_pullRecordedEvents_returnParticipantPullRecordedEventsResult()
    {
        $this->participant->expects($this->once())
                ->method("pullRecordedEvents");
        $this->clientParticipant->pullRecordedEvents();
    }
    
    public function test_viewMissionComment_returnParticipantsViewMissionCommentResult()
    {
        $this->participant->expects($this->once())
                ->method('viewMissionComment')
                ->with($this->missionCommentRepository, $this->missionCommentId);
        $this->clientParticipant->viewMissionComment($this->missionCommentRepository, $this->missionCommentId);
    }
    public function test_viewAllMissionComments_returnParticpantsViewAllMissionCommentsResult()
    {
        $this->participant->expects($this->once())
                ->method('viewAllMissionComments')
                ->with($this->missionCommentRepository, $this->missionId, $this->page, $this->pageSize);
        $this->clientParticipant->viewAllMissionComments($this->missionCommentRepository, $this->missionId, $this->page, $this->pageSize);
    }
    
    protected function clientEquals()
    {
        return $this->clientParticipant->clientEquals($this->client);
    }
    public function test_clientEquals_sameClient_returnTrue()
    {
        $this->assertTrue($this->clientEquals());
    }
    public function test_clientEquals_differentClient_returnFalse()
    {
        $this->clientParticipant->client = $this->buildMockOfClass(Client::class);
        $this->assertFalse($this->clientEquals());
    }
    
    public function test_getClientName_returnClientFullName()
    {
        $this->client->expects($this->once())
                ->method('getFullName');
        $this->clientParticipant->getClientName();
    }
    
    public function test_executeTask_executeParticipantTask()
    {
        $this->participant->expects($this->once())
                ->method('executeTask')
                ->with($this->task);
        $this->clientParticipant->executeTask($this->task);
    }
    
    protected function executeTaskInProgram()
    {
        $this->clientParticipant->executeTaskInProgram($this->taskInProgram);
    }
    public function test_executeTaskInProgram_executeParticipantTaskExecution()
    {
        $this->participant->expects($this->once())
                ->method('executeTaskInProgram')
                ->with($this->taskInProgram);
        $this->executeTaskInProgram();
    }
}

class TestableClientParticipant extends ClientParticipant
{
    public $client;
    public $id;
    public $participant;
    
    function __construct()
    {
        parent::__construct();
    }
}
