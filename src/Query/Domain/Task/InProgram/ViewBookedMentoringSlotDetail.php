<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlotRepository;

class ViewBookedMentoringSlotDetail implements ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var BookedMentoringSlotRepository
     */
    protected $bookedMentoringSlotRepository;

    public function __construct(BookedMentoringSlotRepository $bookedMentoringSlotRepository)
    {
        $this->bookedMentoringSlotRepository = $bookedMentoringSlotRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->bookedMentoringSlotRepository
                ->aBookedMentoringSlotInProgram($programId, $payload->getId());
    }

}
