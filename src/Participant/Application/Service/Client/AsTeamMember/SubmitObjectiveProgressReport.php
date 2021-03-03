<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;
use Participant\Application\Service\Participant\ObjectiveRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;

class SubmitObjectiveProgressReport
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

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

    public function __construct(
            TeamMemberRepository $teamMemberRepository, TeamParticipantRepository $teamParticipantRepository,
            ObjectiveRepository $objectiveRepository,
            ObjectiveProgressReportRepository $objectiveProgressReportRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->objectiveRepository = $objectiveRepository;
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $teamParticipantId, string $objectiveId,
            ObjectiveProgressReportData $objectiveProgressReportData): string
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($teamParticipantId);
        $objective = $this->objectiveRepository->ofId($objectiveId);
        $id = $this->objectiveProgressReportRepository->nextIdentity();
        $objectiveProgressReport = $this->teamMemberRepository
                ->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->submitObjectiveProgressReport($teamParticipant, $objective, $id, $objectiveProgressReportData);
        $this->objectiveProgressReportRepository->add($objectiveProgressReport);
        return $id;
    }

}
