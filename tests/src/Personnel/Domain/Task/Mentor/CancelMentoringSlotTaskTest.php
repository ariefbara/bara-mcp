<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentoringSlotTaskTestBase;

class CancelMentoringSlotTaskTest extends MentoringSlotTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new CancelMentoringSlotTask($this->mentoringSlotRepository, $this->mentoringSlotId);
    }
    
    protected function execute()
    {
        $this->mentoringSlot->expects($this->any())
                ->method('belongsToMentor')
                ->with($this->mentor)
                ->willReturn(true);
        $this->task->execute($this->mentor);
    }
    public function test_execute_cancelMentoringSlot()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('cancel');
        $this->execute();
    }
    public function test_execute_mentoringSlotDoesntBelongsToMentor_forbidden()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('belongsToMentor')
                ->with($this->mentor)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->execute();
        }, 'Forbidden', 'forbidden: can only manage owned mentoring slot');
    }
}
