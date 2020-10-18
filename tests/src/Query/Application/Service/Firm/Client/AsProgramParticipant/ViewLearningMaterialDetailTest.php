<?php

namespace Query\Application\Service\Firm\Client\AsProgramParticipant;

use Query\Domain\ {
    Model\Firm\Client\ClientParticipant,
    Service\LearningMaterialFinder
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ViewLearningMaterialDetailTest extends TestBase
{
    protected $clientProgramParticipationRepository, $clientProgramParticipation;
    protected $learningMaterialFinder;
    protected $dispatcher;
    protected $service;
    protected $clientId = "clientId", $programId = "programId", $learningMaterialId = "learningMaterialId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientProgramParticipation = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientProgramParticipationRepository = $this->buildMockOfInterface(ClientProgramParticipationRepository::class);
        $this->clientProgramParticipationRepository->expects($this->any())
                ->method("aClientProgramParticipationCorrespondWithProgram")
                ->with($this->clientId, $this->programId)
                ->willReturn($this->clientProgramParticipation);
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new ViewLearningMaterialDetail(
                $this->clientProgramParticipationRepository, $this->learningMaterialFinder, $this->dispatcher);
    }
    protected function execute()
    {
        $this->service->execute($this->clientId, $this->programId, $this->learningMaterialId);
    }
    public function test_execute_returnClientParticipantsViewLearningMaterialResult()
    {
        $this->clientProgramParticipation->expects($this->once())
                ->method("viewLearningMaterial")
                ->with($this->learningMaterialFinder, $this->learningMaterialId);
        $this->execute();
    }
    public function test_execute_dispatchClientProgramParticipation()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->clientProgramParticipation);
        $this->execute();
    }
}
