<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Task\Participant\ParticipantQueryTask;
use Tests\TestBase;

class ExecuteParticipantQueryTaskTest extends TestBase
{
    protected $clientParticipantRepository, $clientParticipant, $firmId = 'firmId', $clientId = 'clientId', $participantId = 'participantId';
    protected $service;
    //
    protected $participantQueryTask, $payload = 'string represent payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        
        $this->service = new ExecuteParticipantQueryTask($this->clientParticipantRepository);
        
        //
        $this->participantQueryTask = $this->buildMockOfInterface(ParticipantQueryTask::class);
    }
    
    protected function execute()
    {
        $this->clientParticipantRepository->expects($this->any())
                ->method('aClientParticipant')
                ->with($this->firmId, $this->clientId, $this->participantId)
                ->willReturn($this->clientParticipant);
        
        $this->service->execute(
                $this->firmId, $this->clientId, $this->participantId, $this->participantQueryTask, $this->payload);
    }
    public function test_execute_clientParticipantExecuteTask()
    {
        $this->clientParticipant->expects($this->once())
                ->method('executeQueryTask')
                ->with($this->participantQueryTask, $this->payload);
        $this->execute();
    }
}
