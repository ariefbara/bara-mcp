<?php

namespace Participant\Domain\Task\Participant;

use Tests\src\Participant\Domain\Task\Participant\BookedMentoringSlotTaskTestBase;

class CancelBookedMentoringSlotTaskTest extends BookedMentoringSlotTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new CancelBookedMentoringSlotTask($this->bookedMentoringSlotRepository, $this->bookedMentoringSlotId);
    }
    
    protected function execute()
    {
        $this->bookedMentoringSlot->expects($this->any())
                ->method('belongsToParticipant')
                ->with($this->participant)
                ->willReturn(true);
        $this->task->execute($this->participant);
    }
    public function test_execute_cancelBooking()
    {
        $this->bookedMentoringSlot->expects($this->once())
                ->method('cancel');
        $this->execute();
    }
    public function test_execute_bookedMentoringSlotDoesntBelongsToParticipant_403()
    {
        $this->bookedMentoringSlot->expects($this->once())
                ->method('belongsToParticipant')
                ->with($this->participant)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->execute();
        }, 'Forbidden', 'forbidden: can only managed owned booking slot');
    }
}
