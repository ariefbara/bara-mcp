<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlot;
use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlotRepository;

class ShowBookedMentoringSlotTask implements ITaskExecutableByParticipant
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

    /**
     * 
     * @var BookedMentoringSlot
     */
    public $result;

    public function __construct(BookedMentoringSlotRepository $bookedMentoringSlotRepository, string $id)
    {
        $this->bookedMentoringSlotRepository = $bookedMentoringSlotRepository;
        $this->id = $id;
    }

    public function execute(string $participantId): void
    {
        $this->result = $this->bookedMentoringSlotRepository
                ->aBookedMentoringSlotBelongsToParticipant($participantId, $this->id);
    }

}
