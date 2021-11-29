<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class RejectMentoringRequestTaskTest extends MentorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupMentoringRequestRelatedTask();
        $this->task = new RejectMentoringRequestTask($this->mentoringRequestRepository, $this->mentoringRequestId);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_rejectMentoringRequest()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('reject');
        $this->execute();
    }
    public function test_execute_assertMentoringRequestBelongsToMentor()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('assertBelongsToMentor')
                ->with($this->mentor);
        $this->execute();
    }
}
