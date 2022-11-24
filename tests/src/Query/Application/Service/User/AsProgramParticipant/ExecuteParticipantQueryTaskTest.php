<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\Domain\Task\Participant\ParticipantQueryTask;
use Tests\src\Query\Application\Service\User\AsProgramParticipant\UserParticipantTestBase;

class ExecuteParticipantQueryTaskTest extends UserParticipantTestBase
{
    protected $service;
    //
    protected $task, $payload = 'string represent task payload';


    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteParticipantQueryTask($this->userParticipantRepository);
        //
        $this->task = $this->buildMockOfInterface(ParticipantQueryTask::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->participantId, $this->task, $this->payload);
    }
    public function test_execute_userParticipantExecuteQueryTask()
    {
        $this->userParticipant->expects($this->once())
                ->method('executeQueryTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
}
