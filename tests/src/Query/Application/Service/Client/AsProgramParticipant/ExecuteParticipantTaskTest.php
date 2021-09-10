<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Tests\src\Query\Application\Service\Client\AsProgramParticipant\ClientParticipantTestBase;

class ExecuteParticipantTaskTest extends ClientParticipantTestBase
{
    protected $service;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteParticipantTask($this->clientParticipantRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByParticipant::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->participantId, $this->task);
    }
    public function test_execute_clientParticipantExecuteTask()
    {
        $this->clientParticipant->expects($this->once())
                ->method('executeTask')
                ->with($this->task);
        $this->execute();
    }
}
