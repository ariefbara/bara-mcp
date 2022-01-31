<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use Query\Domain\Task\Participant\ViewMetricSummaryTask;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineMetricRepository;

class MetricSummaryController extends ClientParticipantBaseController
{
    public function show($programParticipationId)
    {
        $metricRepository = new CustomDoctrineMetricRepository($this->em);
        $task = new ViewMetricSummaryTask($metricRepository);
        $this->executeQueryParticipantTask($programParticipationId, $task);
        return $this->listQueryResponse($task->result);
    }
}
