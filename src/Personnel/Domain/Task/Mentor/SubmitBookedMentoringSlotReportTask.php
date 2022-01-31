<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringSlot\BookedMentoringSlotRepository;

class SubmitBookedMentoringSlotReportTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var BookedMentoringSlotRepository
     */
    protected $bookedMentoringSlotRepository;

    /**
     * 
     * @var SubmitMentoringReportPayload
     */
    protected $payload;

    public function __construct(
            BookedMentoringSlotRepository $bookedMentoringSlotRepository,
            SubmitMentoringReportPayload $payload)
    {
        $this->bookedMentoringSlotRepository = $bookedMentoringSlotRepository;
        $this->payload = $payload;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $bookedMentoringSlot = $this->bookedMentoringSlotRepository
                ->ofId($this->payload->getId());
        $bookedMentoringSlot->assertManageableByMentor($mentor);
        $bookedMentoringSlot->submitReport($this->payload->getFormRecordData(), $this->payload->getParticipantRating());
    }

}
