<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Service\ObjectiveProgressReportFinder;

class ViewObjectiveProgressReport
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     * 
     * @var ObjectiveProgressReportFinder
     */
    protected $objectiveProgressReportFinder;

    public function __construct(UserParticipantRepository $userParticipantRepository,
            ObjectiveProgressReportFinder $objectiveProgressReportFinder)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->objectiveProgressReportFinder = $objectiveProgressReportFinder;
    }

    /**
     * 
     * @param string $firmId
     * @param string $userId
     * @param string $participantId
     * @param string $objectiveId
     * @param int $page
     * @param int $pageSize
     * @return ObjectiveProgressReport[]
     */
    public function showAll(
            string $userId, string $participantId, string $objectiveId, int $page, int $pageSize)
    {
        return $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                        ->viewAllObjectiveProgressReportsInObjective($this->objectiveProgressReportFinder, $objectiveId,
                                $page, $pageSize);
    }

    public function showById(string $userId, string $participantId, string $objectiveProgressReportId): ObjectiveProgressReport
    {
        return $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                        ->viewObjectiveProgressReport($this->objectiveProgressReportFinder, $objectiveProgressReportId);
    }

}
