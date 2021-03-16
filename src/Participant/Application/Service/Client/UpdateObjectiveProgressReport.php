<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;

class UpdateObjectiveProgressReport
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var ObjectiveProgressReportRepository
     */
    protected $objectiveProgressReportRepository;

    public function __construct(ClientParticipantRepository $clientParticipantRepository,
            ObjectiveProgressReportRepository $objectiveProgressReportRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $participantId, string $objectiveProgressReportId,
            ObjectiveProgressReportData $objectiveProgressReportData): void
    {
        $objectiveProgressReport = $this->objectiveProgressReportRepository->ofId($objectiveProgressReportId);
        $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                ->updateObjectiveProgressReport($objectiveProgressReport, $objectiveProgressReportData);
        $this->clientParticipantRepository->update();
    }

}
