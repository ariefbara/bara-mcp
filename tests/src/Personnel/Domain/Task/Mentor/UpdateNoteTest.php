<?php

namespace Personnel\Domain\Task\Mentor;

use SharedContext\Domain\ValueObject\LabelData;
use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class UpdateNoteTest extends MentorTaskTestBase
{
    protected $task;
    protected $payload, $labelData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setConsultantNoteRelatedTask();
        
        $this->task = new UpdateNote($this->consultantNoteRepository);
        $this->labelData = new LabelData('name', 'description');
        $this->payload = new UpdateNotePayload($this->consultantNoteId, $this->labelData);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor, $this->payload);
    }
    public function test_execute_updateConsultantNote()
    {
        $this->consultantNote->expects($this->once())
                ->method('update')
                ->with($this->labelData);
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
