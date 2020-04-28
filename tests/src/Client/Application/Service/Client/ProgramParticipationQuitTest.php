<?php

namespace Client\Application\Service\Client;

use Client\Domain\Model\Client\ProgramParticipation;
use Tests\TestBase;

class ProgramParticipationQuitTest extends TestBase
{
    protected $service;
    protected $clientId = 'clientId';
    protected $programParticipationRepository, $programParticipation, $programParticipationId = 'programParticipation-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->programParticipationRepository = $this->buildMockOfInterface(ProgramParticipationRepository::class);
        $this->programParticipationRepository->expects($this->any())
            ->method('ofId')
            ->with($this->clientId, $this->programParticipationId)
            ->willReturn($this->programParticipation);
        
        $this->service = new ProgramParticipationQuit($this->programParticipationRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->clientId, $this->programParticipationId);
    }
    public function test_execute_quitProgramParticipation()
    {
        $this->programParticipation->expects($this->once())
            ->method('quit');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->programParticipationRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
}
