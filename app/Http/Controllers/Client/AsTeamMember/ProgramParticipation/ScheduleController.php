<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Query\Domain\Task\Dependency\ScheduleFilter;
use Query\Domain\Task\Participant\ViewScheduleListTask;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineScheduleRepository;

class ScheduleController extends AsTeamMemberBaseController
{
    public function showAll($teamId, $teamProgramParticipationId)
    {
        $scheduleRepository = new CustomDoctrineScheduleRepository($this->em);
        $payload = (new ScheduleFilter($this->getPageSize(), $this->getPage()))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setOrderDirection($this->stripTagQueryRequest('order'));
        $task = new ViewScheduleListTask($scheduleRepository, $payload);
        $this->executeTeamParticipantQueryTask($teamId, $teamProgramParticipationId, $task);
        return $this->listQueryResponse($task->result);
    }
}
