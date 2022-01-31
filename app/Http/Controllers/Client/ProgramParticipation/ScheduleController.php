<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use Query\Domain\Task\Dependency\ScheduleFilter;
use Query\Domain\Task\Participant\ViewScheduleListTask;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineScheduleRepository;

class ScheduleController extends ClientParticipantBaseController
{
    public function showAll($programParticipationId)
    {
        $scheduleRepository = new CustomDoctrineScheduleRepository($this->em);
        $payload = (new ScheduleFilter($this->getPageSize(), $this->getPage()))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setOrderDirection($this->stripTagQueryRequest('order'));
        $task = new ViewScheduleListTask($scheduleRepository, $payload);
        $this->executeQueryParticipantTask($programParticipationId, $task);
        return $this->listQueryResponse($task->result);
    }
}
