<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class ShowNoteToParticipantTest extends MentorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setConsultantNoteRelatedTask();
        
        $this->task = new ShowNoteToParticipant($this->consultantNoteRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor, $this->consultantNoteId);
    }
    public function test_execute_showConsultantNoteToParticipant()
    {
        $this->consultantNote->expects($this->once())
                ->method('showToParticipant');
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
