<?php

namespace Participant\Application\Service;

use Participant\Domain\Model\ClientParticipant;
use Tests\TestBase;

class ClientQuitParticipationTest extends TestBase
{
    protected $service;
    protected $clientParticipantRepository, $clientParticipant;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programParticipationId = 'programParticipationId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->clientId, $this->programParticipationId)
                ->willReturn($this->clientParticipant);
        
        $this->service = new ClientQuitParticipation($this->clientParticipantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->programParticipationId);
    }
    public function test_execute_quitClientParticipant()
    {
        $this->clientParticipant->expects($this->once())
                ->method('quit');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
