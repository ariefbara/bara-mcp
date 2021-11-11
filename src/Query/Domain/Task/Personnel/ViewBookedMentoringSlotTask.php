<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlot;
use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlotRepository;

class ViewBookedMentoringSlotTask implements TaskExecutableByPersonnel
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
     * @var BookedMentoringSlot|null
     */
    protected $result;

    public function __construct(BookedMentoringSlotRepository $bookedMentoringSlotRepository, string $id)
    {
        $this->bookedMentoringSlotRepository = $bookedMentoringSlotRepository;
        $this->id = $id;
    }

    public function execute(string $personnelId): array
    {
        $this->result = $this->bookedMentoringSlotRepository
                ->aBookedMentoringSlotBelongsToPersonnel($personnelId, $this->id);
    }

}
