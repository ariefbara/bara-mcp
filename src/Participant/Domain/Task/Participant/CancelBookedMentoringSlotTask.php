<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\BookedMentoringSlotRepository;
use Resources\Exception\RegularException;

class CancelBookedMentoringSlotTask implements ITaskExecutableByParticipant
{

    /**
     * 
     * @var BookedMentoringSlotRepository
     */
    protected $bookedMentoringSlotRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    public function __construct(BookedMentoringSlotRepository $bookedMentoringSlotRepository, string $id)
    {
        $this->bookedMentoringSlotRepository = $bookedMentoringSlotRepository;
        $this->id = $id;
    }

    public function execute(Participant $participant): void
    {
        $bookedMentoringSlot = $this->bookedMentoringSlotRepository->ofId($this->id);
        if (!$bookedMentoringSlot->belongsToParticipant($participant)) {
            throw RegularException::forbidden('forbidden: can only managed owned booking slot');
        }
        $bookedMentoringSlot->cancel();
    }

}
