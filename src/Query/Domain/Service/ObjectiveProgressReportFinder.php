<?php

namespace Query\Domain\Service;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

class ObjectiveProgressReportFinder
{

    /**
     * 
     * @var ObjectiveProgressReportRepository
     */
    protected $objectiveProgressReportRepository;

    public function __construct(ObjectiveProgressReportRepository $objectiveProgressReportRepository)
    {
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
    }
    
    public function findObjectiveProgressReportBelongsToParticipant(
            string $participantId, string $objectiveProgressReportId): ObjectiveProgressReport
    {
        return $this->objectiveProgressReportRepository
                ->anObjectiveProgressReportBelongsToParticipant($participantId, $objectiveProgressReportId);
    }
    
    public function findAllObjectiveProgressReportInObjectiveBelongsToParticipant(
            string $participantId, string $objectiveId, int $page, int $pageSize)
    {
        return $this->objectiveProgressReportRepository->allObjectiveProgressReportsInObjectiveBelongsToParticipant(
                $participantId, $objectiveId, $page, $pageSize);
    }

}
