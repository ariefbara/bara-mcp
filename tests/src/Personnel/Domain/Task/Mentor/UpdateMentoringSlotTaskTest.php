<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlotData;
use Tests\src\Personnel\Domain\Task\Mentor\MentoringSlotTaskTestBase;

class UpdateMentoringSlotTaskTest extends MentoringSlotTaskTestBase
{
    protected $task;
    protected $payload;
    
    protected function setUp(): void
    {
        parent::setUp();
        $mentoringSlotData = $this->buildMockOfClass(MentoringSlotData::class);
        $this->payload = new UpdateMentoringSlotPayload($this->mentoringSlotId, $mentoringSlotData);
        $this->task = new UpdateMentoringSlotTask($this->mentoringSlotRepository, $this->payload);
    }
    
    protected function execute()
    {
        $this->mentoringSlot->expects($this->any())
                ->method('belongsToMentor')
                ->with($this->mentor)
                ->willReturn(true);
        $this->task->execute($this->mentor);
    }
    public function test_execute_updateMentoringSlot()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('update')
                ->with($this->payload->getMentoringSlotData());
        $this->execute();
    }
    public function test_execute_notOwnedMentoringSlot_forbidden()
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
