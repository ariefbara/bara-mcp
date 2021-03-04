<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Service\ObjectiveProgressReportFinder;

class ViewObjectiveProgressReport
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var ObjectiveProgressReportFinder
     */
    protected $objectiveProgressReportFinder;

    public function __construct(ClientParticipantRepository $clientParticipantRepository,
            ObjectiveProgressReportFinder $objectiveProgressReportFinder)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->objectiveProgressReportFinder = $objectiveProgressReportFinder;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $participantId
     * @param string $objectiveId
     * @param int $page
     * @param int $pageSize
     * @return ObjectiveProgressReport[]
     */
    public function showAll(
            string $firmId, string $clientId, string $participantId, string $objectiveId, int $page, int $pageSize)
    {
        return $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                        ->viewAllObjectiveProgressReportsInObjective($this->objectiveProgressReportFinder, $objectiveId,
                                $page, $pageSize);
    }

    public function showById(string $firmId, string $clientId, string $participantId, string $objectiveProgressReportId): ObjectiveProgressReport
    {
        return $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                        ->viewObjectiveProgressReport($this->objectiveProgressReportFinder, $objectiveProgressReportId);
    }

}
