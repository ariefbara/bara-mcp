<?php

namespace Participant\Application\Service\Client\ClientParticipant;

use Participant\Application\Service\Client\ClientParticipantRepository;
use Participant\Domain\Model\ClientParticipant;
use Tests\TestBase;

class ExecuteParticipantTaskTest extends TestBase
{
    protected $clientParticipantRepository;
    protected $clientParticipant;
    protected $firmId = 'firmId', $clientId = 'clientId', $clientParticipantId = 'participantId';
    protected $service;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method('aClientParticipant')
                ->with($this->firmId, $this->clientId, $this->clientParticipantId)
                ->willReturn($this->clientParticipant);
        
        $this->service = new ExecuteParticipantTask($this->clientParticipantRepository);
        
        $this->task = $this->buildMockOfInterface(\Participant\Domain\Model\ITaskExecutableByParticipant::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->clientParticipantId, $this->task);
    }
    public function test_execute_clientParticipantExecuteParticipantTask()
    {
        $this->clientParticipant->expects($this->once())
                ->method('executeParticipantTask')
                ->with($this->task);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
        
    }
}
