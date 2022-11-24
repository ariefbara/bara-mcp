<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Task\Mentor\MentorTask;
use Tests\src\Personnel\Application\Service\Firm\Personnel\ProgramConsultant\MentorTestBase;

class ExecuteMentorTaskTest extends MentorTestBase
{
    protected $service;
    protected $task;
    protected $payload = 'string represent payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteMentorTask($this->mentorRepository);
        
        $this->task = $this->buildMockOfInterface(MentorTask::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->mentorId, $this->task, $this->payload);
    }
    public function test_execute_mentorExecuteMentorTask()
    {
        $this->mentor->expects($this->once())
                ->method('executeMentorTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->mentorRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
