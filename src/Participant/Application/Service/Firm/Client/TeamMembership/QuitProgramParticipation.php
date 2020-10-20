<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership;

use Participant\Application\Service\Firm\Client\TeamMembershipRepository;

class QuitProgramParticipation
{
    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;
    
    /**
     *
     * @var TeamProgramParticipationRepository
     */
    protected $teamProgramParticipationRepository;
    
    public function __construct(TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationRepository $teamProgramParticipationRepository)
    {
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramParticipationRepository = $teamProgramParticipationRepository;
    }
    
    public function execute(string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId): void
    {
        $teamProgramParticipation = $this->teamProgramParticipationRepository->ofId($teamProgramParticipationId);
        $this->teamMembershipRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamMembershipId)
                ->quitTeamProgramParticipation($teamProgramParticipation);
        
        $this->teamProgramParticipationRepository->update();
    }

}
