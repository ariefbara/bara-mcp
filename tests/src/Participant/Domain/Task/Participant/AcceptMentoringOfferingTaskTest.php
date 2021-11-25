<?php

namespace Participant\Domain\Task\Participant;

use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class AcceptMentoringOfferingTaskTest extends TaskExecutableByParticipantTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupMentoringRequestRelatedAsset();
        $this->task = new AcceptMentoringOfferingTask($this->mentoringRequestRepository, $this->mentoringRequestId);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant);
    }
    public function test_execute_acceptMentoringRequest()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('accept');
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
