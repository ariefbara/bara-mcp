<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class UpdateNoteTest extends MentorTaskTestBase
{
    protected $task;
    protected $payload, $content = 'content string';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setConsultantNoteRelatedTask();
        
        $this->task = new UpdateNote($this->consultantNoteRepository);
        $this->payload = new UpdateNotePayload($this->consultantNoteId, $this->content);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor, $this->payload);
    }
    public function test_execute_updateConsultantNote()
    {
        $this->consultantNote->expects($this->once())
                ->method('update')
                ->with($this->content);
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
