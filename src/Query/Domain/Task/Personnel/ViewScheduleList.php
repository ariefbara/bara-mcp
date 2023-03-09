<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\ScheduleRepository;

class ViewScheduleList implements PersonnelTask
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
     * @param string $personnelId
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->scheduleRepository->allScheduleBelongsToPersonnel($personnelId, $payload->getFilter());
    }

}
