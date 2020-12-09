<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\Coordinator;
use Tests\TestBase;

class ChangeConsultationSessionChannelTest extends TestBase
{
    protected $consultationSessionRepository;
    protected $coordinatorRepository, $coordinator;
    protected $service;
    protected $firmId = "firmid", $personnelId = "personnelId", $programId = "programId", 
            $consultationSessionId = "consultationSessionid", $media = "new media", $address = "new address";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->any())
                ->method("aCoordinatorCorrespondWithProgram")
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn($this->coordinator);
        
        $this->service = new ChangeConsultationSessionChannel($this->consultationSessionRepository, $this->coordinatorRepository);
        
    }
    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->personnelId, $this->programId, $this->consultationSessionId, $this->media, $this->address);
    }
    public function test_execute_coordinatorChangeConsultationSessionChannel()
    {
        $this->consultationSessionRepository->expects($this->once())->method("ofId");
        $this->coordinator->expects($this->once())
                ->method("changeConsultationSessionChannel")
                ->with($this->anything(), $this->media, $this->address);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultationSessionRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
