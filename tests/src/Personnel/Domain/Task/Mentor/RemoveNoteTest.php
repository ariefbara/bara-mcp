<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class RemoveNoteTest extends MentorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setConsultantNoteRelatedTask();
        
        $this->task = new RemoveNote($this->consultantNoteRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor, $this->consultantNoteId);
    }
    public function test_execute_removeConsultantNote()
    {
        $this->consultantNote->expects($this->once())
                ->method('remove');
        $this->execute();
    }
    public function test_execute_assertConsultantNoteManageableByConsultant()
    {
        $this->consultantNote->expects($this->once())
                ->method('assertManageableByConsultant')
                ->with($this->mentor);
        $this->execute();
    }
}
