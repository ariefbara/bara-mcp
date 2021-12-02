<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class ApproveMentoringRequestTaskTest extends MentorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setMentoringRequestRelatedTask();
        $this->task = new ApproveMentoringRequestTask($this->mentoringRequestRepository, $this->mentoringRequestId);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_approveMentoringRequest()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('approve');
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
