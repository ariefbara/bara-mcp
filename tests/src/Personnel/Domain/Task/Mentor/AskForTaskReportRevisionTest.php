<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class AskForTaskReportRevisionTest extends MentorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        //
        $this->setConsultantTaskRelatedTask();
        
        //
        $this->task = new AskForTaskReportRevision($this->consultantTaskRepository);
    }
    
    //
    protected function execute()
    {
        $this->task->execute($this->mentor, $this->consultantTaskId);
    }
    public function test_execute_askForTaskReportRevision()
    {
        $this->consultantTask->expects($this->once())
                ->method('askForReportRevision');
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
