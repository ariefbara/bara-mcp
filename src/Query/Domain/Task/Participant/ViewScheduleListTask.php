<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Task\Dependency\ScheduleFilter;
use Query\Domain\Task\Dependency\ScheduleRepository;

class ViewScheduleListTask implements ITaskExecutableByParticipant
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

    public function execute(string $participantId): void
    {
        $this->result = $this->scheduleRepository->allScheduleBelongsToParticipant($participantId, $this->payload);
    }

}
