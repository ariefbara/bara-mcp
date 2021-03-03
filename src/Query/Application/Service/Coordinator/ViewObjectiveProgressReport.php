<?php

namespace Query\Application\Service\Coordinator;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

class ViewObjectiveProgressReport
{

    /**
     * 
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    /**
     * 
     * @var ObjectiveProgressReportRepository
     */
    protected $objectiveProgressReportRepository;

    public function __construct(CoordinatorRepository $coordinatorRepository,
            ObjectiveProgressReportRepository $objectiveProgressReportRepository)
    {
        $this->coordinatorRepository = $coordinatorRepository;
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param string $programId
     * @param string $objectiveId
     * @param int $page
     * @param int $pageSize
     * @return ObjectiveProgressReport[]
     */
    public function showAll(
            string $firmId, string $personnelId, string $programId, string $objectiveId, int $page, int $pageSize)
    {
        return $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                        ->viewAllObjectiveProgressReportBelongsToObjective(
                                $this->objectiveProgressReportRepository, $objectiveId, $page, $pageSize);
    }

    public function showById(
            string $firmId, string $personnelId, string $programId, $objectiveProgressReportId): ObjectiveProgressReport
    {
        return $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                        ->viewObjectiveProgressReport($this->objectiveProgressReportRepository,
                                $objectiveProgressReportId);
    }

}
