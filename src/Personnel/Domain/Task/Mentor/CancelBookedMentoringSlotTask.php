<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringSlot\BookedMentoringSlotRepository;
use Resources\Exception\RegularException;

class CancelBookedMentoringSlotTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var BookedMentoringSlotRepository
     */
    protected $bookedSlotRepository;

    /**
     * 
     * @var string
     */
    protected $bookedSlotId;

    public function __construct(BookedMentoringSlotRepository $bookedSlotRepository, string $bookedSlotId)
    {
        $this->bookedSlotRepository = $bookedSlotRepository;
        $this->bookedSlotId = $bookedSlotId;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $bookedSlot = $this->bookedSlotRepository->ofId($this->bookedSlotId);
        if (!$bookedSlot->belongsToMentor($mentor)) {
            throw RegularException::forbidden('forbidden: can only manage owned booked slot');
        }
        $bookedSlot->cancel();
    }

}
