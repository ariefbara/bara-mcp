<?php

namespace Query\Domain\Service\Firm\Team;

use Query\Domain\Model\Firm\{
    Team,
    Team\TeamProgramParticipation
};

class TeamProgramParticipationFinder
{

    /**
     *
     * @var TeamProgramParticipationRepository
     */
    protected $teamProgramParticipationRepository;

    public function __construct(TeamProgramParticipationRepository $teamProgramParticipationRepository)
    {
        $this->teamProgramParticipationRepository = $teamProgramParticipationRepository;
    }

    public function findProgramParticipationBelongsToTeam(Team $team, string $teamProgramParticipationId): TeamProgramParticipation
    {
        return $this->teamProgramParticipationRepository->ofId($team->getId(), $teamProgramParticipationId);
    }

    /**
     * 
     * @param Team $team
     * @param int $page
     * @param int $pageSize
     * @return TeamProgramParticipation[]
     */
    public function findAllProgramParticipationsBelongsToTeam(Team $team, int $page, int $pageSize)
    {
        return $this->teamProgramParticipationRepository->all($team->getId(), $page, $pageSize);
    }
    
    public function findTeamProgramParticipationCorrespondWithProgram(Team $team, string $programId): TeamProgramParticipation
    {
        return $this->teamProgramParticipationRepository
                ->aTeamProgramParticipationCorrespondWithProgram($team->getId(), $programId);
    }

}
