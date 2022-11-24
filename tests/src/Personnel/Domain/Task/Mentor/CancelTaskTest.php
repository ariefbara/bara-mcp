<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class CancelTaskTest extends MentorTaskTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setConsultantTaskRelatedTask();
        
        $this->service = new CancelTask($this->consultantTaskRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->mentor, $this->consultantTaskId);
    }
    public function test_execute_cancelConsultantTask()
    {
        $this->consultantTask->expects($this->once())
                ->method('cancel');
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
