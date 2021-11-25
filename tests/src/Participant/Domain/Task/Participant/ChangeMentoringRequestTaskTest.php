<?php

namespace Participant\Domain\Task\Participant;

use DateTimeImmutable;
use Participant\Domain\Model\Participant\MentoringRequestData;
use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class ChangeMentoringRequestTaskTest extends TaskExecutableByParticipantTestBase
{
    protected $mentoringRequestData;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupMentoringRequestRelatedAsset();
        
        $this->mentoringRequestData = new MentoringRequestData(new DateTimeImmutable('+24 hours'), 'online', 'google meet');
        $payload = new ChangeMentoringRequestPayload($this->mentoringRequestId, $this->mentoringRequestData);
        $this->task = new ChangeMentoringRequestTask($this->mentoringRequestRepository, $payload);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant);
    }
    public function test_execute_updateMentoringRequest()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('update')
                ->with($this->mentoringRequestData);
        $this->execute();
    }
    public function test_execute_assertMentoringRequestManageableByParticipant()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('assertManageableByParticipant')
                ->with($this->participant);
        $this->execute();
    }
}
