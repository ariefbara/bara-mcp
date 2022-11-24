<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class HideNoteFromParticipantTest extends MentorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setConsultantNoteRelatedTask();
        
        $this->task = new HideNoteFromParticipant($this->consultantNoteRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor, $this->consultantNoteId);
    }
    public function test_execute_hideConsultantNoteFromParticipant()
    {
        $this->consultantNote->expects($this->once())
                ->method('hideFromParticipant');
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
