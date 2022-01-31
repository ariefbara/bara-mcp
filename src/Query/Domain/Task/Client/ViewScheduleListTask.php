<?php

namespace Query\Domain\Task\Client;

use Query\Domain\Model\Firm\ITaskExecutableByClient;
use Query\Domain\Task\Dependency\ScheduleFilter;
use Query\Domain\Task\Dependency\ScheduleRepository;

class ViewScheduleListTask implements ITaskExecutableByClient
{

    /**
     * 
     * @var ScheduleRepository
     */
    protected $scheduleRepository;

    /**
     * 
     * @var ScheduleFilter
     */
    protected $payload;
    public $result;

    public function __construct(ScheduleRepository $scheduleRepository, ScheduleFilter $payload)
    {
        $this->scheduleRepository = $scheduleRepository;
        $this->payload = $payload;
    }

    public function execute(string $clientId): void
    {
        $this->result = $this->scheduleRepository->allScheduleBelongsToClient($clientId, $this->payload);
    }

}
