<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;

class UpdateObjectiveProgressReport
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
     * @var ObjectiveProgressReportRepository
     */
    protected $objectiveProgressReportRepository;

    public function __construct(TeamMemberRepository $teamMemberRepository,
            TeamParticipantRepository $teamParticipantRepository,
            ObjectiveProgressReportRepository $objectiveProgressReportRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $teamParticipantId,
            string $objectiveProgressReportId, ObjectiveProgressReportData $objectiveProgressReportData): void
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($teamParticipantId);
        $objectiveProgressReport = $this->objectiveProgressReportRepository->ofId($objectiveProgressReportId);
        $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->updateObjectiveProgressReport($teamParticipant, $objectiveProgressReport, $objectiveProgressReportData);
        $this->teamMemberRepository->update();
    }

}
