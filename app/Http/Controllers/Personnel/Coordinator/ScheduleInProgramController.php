<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use DateInterval;
use DateTimeImmutable;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\ScheduleFilter;
use Query\Domain\Task\InProgram\ViewAllSchedules;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineScheduleRepository;

class ScheduleInProgramController extends CoordinatorBaseController
{
    public function viewAll($coordinatorId)
    {
        $scheduleRepository = new CustomDoctrineScheduleRepository($this->em);
        $task = new ViewAllSchedules($scheduleRepository);
        
        $from = ($this->dateTimeImmutableOfQueryRequest('from') ?? new DateTimeImmutable())->setTime(0, 0);
        $to = ($this->dateTimeImmutableOfQueryRequest('to') ?? new DateTimeImmutable('+8 days'))
                ->setTime(0, 0)
                ->add(new DateInterval('P1D'));
        $to = $to < $from->add(new DateInterval('P1M1D')) ? $to : $from->add(new DateInterval('P1M1D'));
        
        $filter = (new ScheduleFilter(1, 25))
                ->setFrom($from)
                ->setTo($to)
                ->setOrderDirection(ScheduleFilter::ASC);
        $payload = new CommonViewListPayload($filter);
        $this->executeProgramQueryTask($coordinatorId, $task, $payload);
        
        return $payload->result;
    }
}
