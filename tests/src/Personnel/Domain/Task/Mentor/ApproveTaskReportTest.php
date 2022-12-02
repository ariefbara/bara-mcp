<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class ApproveTaskReportTest extends MentorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        //
        $this->setConsultantTaskRelatedTask();
        
        //
        $this->task = new ApproveTaskReport($this->consultantTaskRepository);
    }
    
    //
    protected function execute()
    {
        $this->task->execute($this->mentor, $this->consultantTaskId);
    }
    public function test_execute_approveTaskReport()
    {
        $this->consultantTask->expects($this->once())
                ->method('approveReport');
        $this->execute();
    }
    public function test_execute_assertTaskManageableyByConsultant()
    {
        $this->consultantTask->expects($this->once())
                ->method('assertManageableByConsultant')
                ->with($this->mentor);
        $this->execute();
    }
}
