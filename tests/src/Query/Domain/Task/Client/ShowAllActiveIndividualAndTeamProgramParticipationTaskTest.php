<?php

namespace Query\Domain\Task\Client;

use Query\Domain\Task\Dependency\Firm\Program\ParticipantRepository;
use Tests\src\Query\Domain\Task\Client\TaskExecutableByClientTestBase;

class ShowAllActiveIndividualAndTeamProgramParticipationTaskTest extends TaskExecutableByClientTestBase
{
    protected $participantRepository;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->task = new ShowAllActiveIndividualAndTeamProgramParticipationTask($this->participantRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->clientId);
    }
    public function test_execute_setResultFromRepository()
    {
        $this->participantRepository->expects($this->once())
                ->method('allActiveIndividualAndTeamProgramParticipationBelongsToClient')
                ->with($this->clientId)
                ->willReturn($result = ['participant list']);
        $this->execute();
        $this->assertEquals($result, $this->task->result);
    }
}
