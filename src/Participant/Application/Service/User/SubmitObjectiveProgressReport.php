<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;
use Participant\Application\Service\Participant\ObjectiveRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;

class SubmitObjectiveProgressReport
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

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

    public function __construct(UserParticipantRepository $userParticipantRepository,
            ObjectiveRepository $objectiveRepository,
            ObjectiveProgressReportRepository $objectiveProgressReportRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->objectiveRepository = $objectiveRepository;
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
    }

    public function execute(
            string $userId, string $participantId, string $objectiveId,
            ObjectiveProgressReportData $objectiveProgressReportData): string
    {
        $objective = $this->objectiveRepository->ofId($objectiveId);
        $id = $this->objectiveProgressReportRepository->nextIdentity();
        $objectiveProgressReport = $this->userParticipantRepository
                ->aUserParticipant($userId, $participantId)
                ->submitObjectiveProgressReport($objective, $id, $objectiveProgressReportData);
        $this->objectiveProgressReportRepository->add($objectiveProgressReport);
        return $id;
    }

}
