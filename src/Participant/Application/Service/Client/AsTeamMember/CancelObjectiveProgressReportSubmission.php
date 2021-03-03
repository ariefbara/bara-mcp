<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;

class CancelObjectiveProgressReportSubmission
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

    public function __construct(
            TeamMemberRepository $teamMemberRepository, TeamParticipantRepository $teamParticipantRepository,
            ObjectiveProgressReportRepository $objectiveProgressReportRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
    }
    
    public function execute(
            string $firmId, string $clientId, string $teamId, string $teamParticipantId,
            string $objectiveProgressReportId): void
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($teamParticipantId);
        $objectiveProgressReport = $this->objectiveProgressReportRepository->ofId($objectiveProgressReportId);
        $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->cancelObjectiveProgressReportSubmission($teamParticipant, $objectiveProgressReport);
        $this->teamMemberRepository->update();
    }

}
