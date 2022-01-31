<?php

namespace App\Http\Controllers\Client;

use Query\Domain\Task\Client\ViewScheduleListTask;
use Query\Domain\Task\Dependency\ScheduleFilter;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineScheduleRepository;

class ScheduleController extends ClientBaseController
{
    public function showAll()
    {
        $scheduleRepository = new CustomDoctrineScheduleRepository($this->em);
        $payload = (new ScheduleFilter($this->getPageSize(), $this->getPage()))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setOrderDirection($this->stripTagQueryRequest('order'));
        $task = new ViewScheduleListTask($scheduleRepository, $payload);
        $this->executeQueryTask($task);
        return $this->listQueryResponse($task->result);
    }
}
