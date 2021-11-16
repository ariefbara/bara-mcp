<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot\BookedMentoringSlot;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringSlot\BookedMentoringSlotRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class SubmitBookedMentoringSlotReportTaskTest extends MentorTaskTestBase
{
    protected $bookedMentoringSlotRepository;
    protected $bookedMentoringSlot;
    protected $bookedMentoringSlotId = 'bookedMentoringSlotId';
    protected $participantRating = 7;
    protected $formRecordData;
    protected $payload;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->bookedMentoringSlot = $this->buildMockOfClass(BookedMentoringSlot::class);
        $this->bookedMentoringSlotRepository = $this->buildMockOfInterface(BookedMentoringSlotRepository::class);
        $this->bookedMentoringSlotRepository->expects($this->any())
                ->method('ofId')
                ->with($this->bookedMentoringSlotId)
                ->willReturn($this->bookedMentoringSlot);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->payload = new SubmitBookedMentoringSlotReportPayload($this->bookedMentoringSlotId, $this->participantRating, $this->formRecordData);
        
        $this->task = new SubmitBookedMentoringSlotReportTask($this->bookedMentoringSlotRepository, $this->payload);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_submitBookingSlotReport()
    {
        $this->bookedMentoringSlot->expects($this->once())
                ->method('submitReport')
                ->with($this->formRecordData, $this->participantRating);
        $this->execute();
    }
    public function test_execute_assertBookedMentoringSlotManageableByMentor()
    {
        $this->bookedMentoringSlot->expects($this->once())
                ->method('assertManageableByMentor')
                ->with($this->mentor);
        $this->execute();
    }
}
