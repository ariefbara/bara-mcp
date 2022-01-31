<?php

namespace Participant\Domain\Task\Participant;

use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class CancelMentoringRequestTaskTest extends TaskExecutableByParticipantTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setMentoringRequestRelatedAsset();
        $this->task = new CancelMentoringRequestTask($this->mentoringRequestRepository, $this->mentoringRequestId);
    }
    protected function execute()
    {
        $this->task->execute($this->participant);
    }
    public function test_execute_cancelMentoringRequest()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('cancel');
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
