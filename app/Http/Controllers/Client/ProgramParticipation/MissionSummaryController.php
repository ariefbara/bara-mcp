<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use Query\Domain\Task\Participant\ViewMissionSummaryTask;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineMissionSummaryRepository;

class MissionSummaryController extends ClientParticipantBaseController
{
    public function show($programParticipationId)
    {
        $missionSummaryRepository = new CustomDoctrineMissionSummaryRepository($this->em);
        $task = new ViewMissionSummaryTask($missionSummaryRepository);
        $this->executeQueryParticipantTask($programParticipationId, $task);
        return $this->singleQueryResponse($task->result);
    }
}
