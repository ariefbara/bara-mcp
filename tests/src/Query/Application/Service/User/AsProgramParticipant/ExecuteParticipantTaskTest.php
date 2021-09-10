<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Tests\src\Query\Application\Service\User\AsProgramParticipant\UserParticipantTestBase;

class ExecuteParticipantTaskTest extends UserParticipantTestBase
{
    protected $service;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteParticipantTask($this->userParticipantRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByParticipant::class);
    }
    protected function execute()
    {
        $this->service->execute($this->userId, $this->participantId, $this->task);
    }
    public function test_execute_userParticipantExecuteTask()
    {
        $this->userParticipant->expects($this->once())
                ->method('executeTask')
                ->with($this->task);
        $this->execute();
    }
}
