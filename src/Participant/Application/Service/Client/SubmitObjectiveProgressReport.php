<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;
use Participant\Application\Service\Participant\ObjectiveRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;

class SubmitObjectiveProgressReport
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var ObjectiveRepository
     */
    protected $objectiveRepository;

    /**
     * 
     * @var ObjectiveProgressReportRepository
     */
    protected $objectiveProgressReportRepository;

    public function __construct(ClientParticipantRepository $clientParticipantRepository,
            ObjectiveRepository $objectiveRepository,
            ObjectiveProgressReportRepository $objectiveProgressReportRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->objectiveRepository = $objectiveRepository;
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $participantId, string $objectiveId,
            ObjectiveProgressReportData $objectiveProgressReportData): string
    {
        $objective = $this->objectiveRepository->ofId($objectiveId);
        $id = $this->objectiveProgressReportRepository->nextIdentity();
        $objectiveProgressReport = $this->clientParticipantRepository
                ->aClientParticipant($firmId, $clientId, $participantId)
                ->submitObjectiveProgressReport($objective, $id, $objectiveProgressReportData);
        $this->objectiveProgressReportRepository->add($objectiveProgressReport);
        return $id;
    }

}
