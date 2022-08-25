<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Query\Domain\Task\Participant\ViewMissionSummaryTask;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineMissionSummaryRepository;

class MissionSummaryController extends AsTeamMemberBaseController
{
    public function show($teamId, $teamProgramParticipationId)
    {
        $missionSummaryRepository = new CustomDoctrineMissionSummaryRepository($this->em);
        $task = new ViewMissionSummaryTask($missionSummaryRepository);
        $this->executeTeamParticipantQueryTask($teamId, $teamProgramParticipationId, $task);
        return $this->singleQueryResponse($task->result);
    }
}
