<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\BookedMentoringSlotRepository;

class SubmitBookedMentoringReportTask implements ITaskExecutableByParticipant
{

    /**
     * 
     * @var BookedMentoringSlotRepository
     */
    protected $bookedMentoringSlotRepository;

    /**
     * 
     * @var SubmitBookedMentoringReportPayload
     */
    protected $payload;

    public function __construct(
            BookedMentoringSlotRepository $bookedMentoringSlotRepository, SubmitBookedMentoringReportPayload $payload)
    {
        $this->bookedMentoringSlotRepository = $bookedMentoringSlotRepository;
        $this->payload = $payload;
    }

    public function execute(Participant $participant): void
    {
        $bookedMentoringSlot = $this->bookedMentoringSlotRepository->ofId($this->payload->getBookedMentoringSlotId());
        $bookedMentoringSlot->assertManageableByParticipant($participant);
        $bookedMentoringSlot->submitReport($this->payload->getMentorRating(), $this->payload->getFormRecordData());
    }

}
