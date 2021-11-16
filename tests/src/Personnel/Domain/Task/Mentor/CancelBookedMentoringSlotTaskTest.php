<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot\BookedMentoringSlot;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringSlot\BookedMentoringSlotRepository;
use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class CancelBookedMentoringSlotTaskTest extends MentorTaskTestBase
{
    protected $bookedSlotRepository;
    protected $bookedSlot;
    protected $bookedSlotId = 'bookedSlotId';
    
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookedSlot = $this->buildMockOfClass(BookedMentoringSlot::class);
        $this->bookedSlotRepository = $this->buildMockOfInterface(BookedMentoringSlotRepository::class);
        $this->bookedSlotRepository->expects($this->any())
                ->method('ofId')
                ->with($this->bookedSlotId)
                ->willReturn($this->bookedSlot);
        
        $this->task = new CancelBookedMentoringSlotTask($this->bookedSlotRepository, $this->bookedSlotId);
    }
    
    protected function execute()
    {
        $this->bookedSlot->expects($this->any())
                ->method('belongsToMentor')
                ->with($this->mentor)
                ->willReturn(true);
        $this->task->execute($this->mentor);
    }
    public function test_execute_cancelBookedSlot()
    {
        $this->bookedSlot->expects($this->once())
                ->method('cancel');
        $this->execute();
    }
    public function test_execute_assertBookedMentoringManageableByMentor()
    {
        $this->bookedSlot->expects($this->once())
                ->method('assertManageableByMentor')
                ->with($this->mentor);
        $this->execute();
    }
}
