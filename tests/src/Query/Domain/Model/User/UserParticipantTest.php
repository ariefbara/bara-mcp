<?php

namespace Query\Domain\Model\User;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\User;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;
use Query\Domain\Service\LearningMaterialFinder;
use Query\Domain\Task\Participant\ParticipantQueryTask;
use Tests\TestBase;

class UserParticipantTest extends TestBase
{
    protected $userParticipant;
    protected $user;
    protected $participant;
    protected $learningMaterialFinder;
    protected $learningMaterialId = "learningMaterialId";
    protected $page = 1, $pageSize = 25;
    protected $missionCommentRepository, $missionId = 'missionId', $missionCommentId = 'missionCommentId';
    protected $task;
    //
    protected $participantTask, $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipant = new TestableUserParticipant();
        $this->user = $this->buildMockOfClass(User::class);
        $this->userParticipant->user = $this->user;
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->userParticipant->participant = $this->participant;
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
        $this->missionCommentRepository = $this->buildMockOfInterface(MissionCommentRepository::class);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByParticipant::class);
        //
        $this->participantTask = $this->buildMockOfInterface(ParticipantQueryTask::class);
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
    
    public function test_viewMissionComment_returnParticipantsViewMissionCommentResult()
    {
        $this->participant->expects($this->once())
                ->method('viewMissionComment')
                ->with($this->missionCommentRepository, $this->missionCommentId);
        $this->userParticipant->viewMissionComment($this->missionCommentRepository, $this->missionCommentId);
    }
    public function test_viewAllMissionComments_returnParticpantsViewAllMissionCommentsResult()
    {
        $this->participant->expects($this->once())
                ->method('viewAllMissionComments')
                ->with($this->missionCommentRepository, $this->missionId, $this->page, $this->pageSize);
        $this->userParticipant->viewAllMissionComments($this->missionCommentRepository, $this->missionId, $this->page, $this->pageSize);
    }
    
    public function test_getUserName_returnUserFullName()
    {
        $this->user->expects($this->once())
                ->method('getFullName');
        $this->userParticipant->getUserName();
    }
    
    public function test_executeTask_participantExecuteTask()
    {
        $this->participant->expects($this->once())
                ->method('executeTask')
                ->with($this->task);
        $this->userParticipant->executeTask($this->task);
    }
    
    //
    protected function executeQueryTask()
    {
        $this->userParticipant->executeQueryTask($this->participantTask, $this->payload);
    }
    public function test_executeQueryTask_participantExecuteTask()
    {
        $this->participant->expects($this->once())
                ->method('executeQueryTask')
                ->with($this->participantTask, $this->payload);
        $this->executeQueryTask();
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
