<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Query\Domain\Task\Participant\ViewMetricSummaryTask;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineMetricRepository;

class MetricSummaryController extends AsTeamMemberBaseController
{
    public function show($teamId, $teamProgramParticipationId)
    {
        $metricRepository = new CustomDoctrineMetricRepository($this->em);
        $task = new ViewMetricSummaryTask($metricRepository);
        $this->executeTeamParticipantQueryTask($teamId, $teamProgramParticipationId, $task);
        return $this->listQueryResponse($task->result);
    }
}
