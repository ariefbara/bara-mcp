<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant;
use Tests\TestBase;

class LogViewLearningMaterialActivityTest extends TestBase
{
    protected $viewLearningMaterialActivityLogRepository, $nextId = "nextId";
    protected $participantRepository, $participant;
    protected $service;
    protected $participantId = "participantId", $learningMaterialId = "learningMaterialId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->viewLearningMaterialActivityLogRepository = $this->buildMockOfInterface(ViewLearningMaterialActivityLogRepository::class);
        $this->viewLearningMaterialActivityLogRepository->expects($this->any())->method("nextIdentity")->willReturn($this->nextId);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participantRepository->expects($this->any())
                ->method("ofId")
                ->with($this->participantId)
                ->willReturn($this->participant);
        
        $this->service = new LogViewLearningMaterialActivity(
                $this->viewLearningMaterialActivityLogRepository, $this->participantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->participantId, $this->learningMaterialId);
    }
    public function test_execute_addViewLearningMaterialLogActivityToRepository()
    {
        $this->participant->expects($this->once())
                ->method("logViewLearningMaterialActivity")
                ->with($this->nextId, $this->learningMaterialId);
        $this->viewLearningMaterialActivityLogRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
}
