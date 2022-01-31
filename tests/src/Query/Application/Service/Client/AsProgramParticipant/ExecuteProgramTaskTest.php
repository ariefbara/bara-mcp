<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;
use Tests\src\Query\Application\Service\Client\AsProgramParticipant\ClientParticipantTestBase;

class ExecuteProgramTaskTest extends ClientParticipantTestBase
{
    protected $service;
    protected $taskInProgram;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteProgramTask($this->clientParticipantRepository);
        
        $this->taskInProgram = $this->buildMockOfInterface(ITaskInProgramExecutableByParticipant::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->participantId, $this->taskInProgram);
    }
    public function test_execute_participantExecuteTaskInProgram()
    {
        $this->clientParticipant->expects($this->once())
                ->method('executeTaskInProgram')
                ->with($this->taskInProgram);
        $this->execute();
    }
}
