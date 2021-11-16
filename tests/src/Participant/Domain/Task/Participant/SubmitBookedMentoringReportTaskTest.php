<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant\BookedMentoringSlot;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\BookedMentoringSlotRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class SubmitBookedMentoringReportTaskTest extends TaskExecutableByParticipantTestBase
{
    protected $bookedMentoringSlotRepository;
    protected $bookedMentoringSlot;
    protected $bookedMentoringSlotId = 'bookedMentoringSlotId';
    protected $payload;
    protected $task;
    
    protected $mentorRating = 7;
    protected $formRecordData;

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
        $this->payload = new SubmitBookedMentoringReportPayload(
                $this->bookedMentoringSlotId, $this->mentorRating, $this->formRecordData);
        
        $this->task = new SubmitBookedMentoringReportTask($this->bookedMentoringSlotRepository, $this->payload);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant);
    }
    public function test_execute_submitMentoringReport()
    {
        $this->bookedMentoringSlot->expects($this->once())
                ->method('submitReport')
                ->with($this->mentorRating, $this->formRecordData);
        $this->execute();
    }
    public function test_execute_assertBookedMentoringSlotManageableByParticipant()
    {
        $this->bookedMentoringSlot->expects($this->once())
                ->method('assertManageableByParticipant')
                ->with($this->participant);
        $this->execute();
    }
}
