<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotRepository;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\BookedMentoringSlotRepository;

class BookMentoringSlotTask implements ITaskExecutableByParticipant
{

    /**
     * 
     * @var BookedMentoringSlotRepository
     */
    protected $bookedMentoringSlotRepository;

    /**
     * 
     * @var MentoringSlotRepository
     */
    protected $mentoringSlotRepository;

    /**
     * 
     * @var BookMentoringSlotPayload
     */
    protected $payload;

    /**
     * 
     * @var string|null
     */
    public $bookedMentoringSlotId = null;

    public function __construct(
            BookedMentoringSlotRepository $bookedMentoringSlotRepository,
            MentoringSlotRepository $mentoringSlotRepository, BookMentoringSlotPayload $payload)
    {
        $this->bookedMentoringSlotRepository = $bookedMentoringSlotRepository;
        $this->mentoringSlotRepository = $mentoringSlotRepository;
        $this->payload = $payload;
    }

    public function execute(Participant $participant): void
    {
        $this->bookedMentoringSlotId = $this->bookedMentoringSlotRepository->nextIdentity();
        $mentoringSlot = $this->mentoringSlotRepository->ofId($this->payload->getMentoringSlotId());
        $bookedMentoringSlot = $participant->bookMentoringSlot($this->bookedMentoringSlotId, $mentoringSlot);
        $this->bookedMentoringSlotRepository->add($bookedMentoringSlot);
    }

}
