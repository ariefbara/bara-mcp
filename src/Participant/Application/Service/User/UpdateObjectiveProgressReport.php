<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;

class UpdateObjectiveProgressReport
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     * 
     * @var ObjectiveProgressReportRepository
     */
    protected $objectiveProgressReportRepository;

    public function __construct(UserParticipantRepository $userParticipantRepository,
            ObjectiveProgressReportRepository $objectiveProgressReportRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
    }

    public function execute(
            string $userId, string $participantId, string $objectiveProgressReportId,
            ObjectiveProgressReportData $objectiveProgressReportData): void
    {
        $objectiveProgressReport = $this->objectiveProgressReportRepository->ofId($objectiveProgressReportId);
        $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                ->updateObjectiveProgressReport($objectiveProgressReport, $objectiveProgressReportData);
        $this->userParticipantRepository->update();
    }

}
