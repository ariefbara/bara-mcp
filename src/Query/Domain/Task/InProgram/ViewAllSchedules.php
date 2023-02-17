<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\ScheduleRepository;

class ViewAllSchedules implements ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var ScheduleRepository
     */
    protected $scheduleRepository;

    public function __construct(ScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->scheduleRepository->allScheduleInProgram($programId, $payload->getFilter());
    }

}
