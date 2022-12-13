<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Program\Participant\TaskData;
use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class UpdateTaskTest extends MentorTaskTestBase
{
    protected $service;
    protected $payload, $taskData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setConsultantTaskRelatedTask();
        
        $this->service = new UpdateTask($this->consultantTaskRepository);
        //
        $this->taskData = $this->buildMockOfClass(TaskData::class);
        $this->payload = new UpdateTaskPayload($this->consultantTaskId, $this->taskData);
    }
    
    protected function execute()
    {
        $this->service->execute($this->mentor, $this->payload);
    }
    public function test_execute_updateConsultantTask()
    {
        $this->consultantTask->expects($this->once())
                ->method('update')
                ->with($this->taskData);
        $this->execute();
    }
    public function test_execute_assertConsultantTaskManageableByConsultant()
    {
        $this->consultantTask->expects($this->once())
                ->method('assertManageableByConsultant')
                ->with($this->mentor);
        $this->execute();
    }
}
