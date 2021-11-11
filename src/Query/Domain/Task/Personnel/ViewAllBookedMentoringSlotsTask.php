<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlot;
use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlotRepository;

class ViewAllBookedMentoringSlotsTask implements TaskExecutableByPersonnel
{

    /**
     * 
     * @var BookedMentoringSlotRepository
     */
    protected $bookedMentoringSlotRepository;

    /**
     * 
     * @var ViewAllBookedMentoringSlotsPayload
     */
    protected $payload;

    /**
     * 
     * @var BookedMentoringSlot[]|null
     */
    protected $results;

    public function __construct(
            BookedMentoringSlotRepository $bookedMentoringSlotRepository, ViewAllBookedMentoringSlotsPayload $payload)
    {
        $this->bookedMentoringSlotRepository = $bookedMentoringSlotRepository;
        $this->payload = $payload;
    }

    public function execute(string $personnelId): array
    {
        $this->results = $this->bookedMentoringSlotRepository->allBookedMentoringSlotsBelongsToPersonnel(
                $personnelId, $this->payload->getPage(), $this->payload->getPageSize(), $this->payload->getFilter());
    }

}
