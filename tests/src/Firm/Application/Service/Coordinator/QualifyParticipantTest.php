<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\Coordinator;
use Tests\TestBase;

class QualifyParticipantTest extends TestBase
{
    protected $participantRepository;
    protected $coordinatorRepository, $coordinator;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $programId = "programId", $participantId = "participantId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->any())
                ->method("aCoordinatorCorrespondWithProgram")
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn($this->coordinator);
        
        $this->service = new QualifyParticipant($this->participantRepository, $this->coordinatorRepository);
        
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->participantId);
    }
    public function test_execute_coordinatorQualifyParticipant()
    {
        $this->participantRepository->expects($this->once())->method("ofId");
        $this->coordinator->expects($this->once())
                ->method("qualifyParticipant");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->participantRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
    
    
}
