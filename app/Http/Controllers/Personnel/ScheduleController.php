<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\ScheduleFilter;
use Query\Domain\Task\Personnel\ViewScheduleList;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineScheduleRepository;

class ScheduleController extends PersonnelBaseController
{
    
    public function showAll()
    {
        $scheduleRepository = new CustomDoctrineScheduleRepository($this->em);
        $task = new ViewScheduleList($scheduleRepository);
        $filter = (new ScheduleFilter($this->getPageSize(), $this->getPage()))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setOrderDirection($this->stripTagQueryRequest('order'));
        $payload = new CommonViewListPayload($filter);
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
    
}
