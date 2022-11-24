<?php

namespace Participant\Application\Service\Client\ClientParticipant;

use Participant\Application\Service\Client\ClientParticipantRepository;
use Participant\Domain\Model\ClientParticipant;
use Participant\Domain\Task\Participant\ParticipantTask;
use Tests\TestBase;

class ExecuteTaskTest extends TestBase
{
    protected $clientParticipantRepository;
    protected $clientParticipant, $firmId = 'firmId', $clientId = 'clientId', $clientParticipantId = 'clientParticipantId';
    
    protected $service;
    //
    protected $task, $payload = 'string represent payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        
        $this->service = new ExecuteTask($this->clientParticipantRepository);
        //
        $this->task = $this->buildMockOfInterface(ParticipantTask::class);
    }
    
    protected function execute()
    {
        $this->clientParticipantRepository->expects($this->any())
                ->method('aClientParticipant')
                ->with($this->firmId, $this->clientId, $this->clientParticipantId)
                ->willReturn($this->clientParticipant);
        $this->service->execute($this->firmId, $this->clientId, $this->clientParticipantId, $this->task, $this->payload);
    }
    public function test_execute_clientParticipantExecuteTask()
    {
        $this->clientParticipant->expects($this->once())
                ->method('executeTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}

