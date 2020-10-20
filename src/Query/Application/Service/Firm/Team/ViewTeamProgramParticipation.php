<?php

namespace Query\Application\Service\Firm\Team;

use Query\Domain\Model\Firm\Team\TeamProgramParticipation;

class ViewTeamProgramParticipation
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

    /**
     * 
     * @param string $teamId
     * @param int $page
     * @param int $pageSize
     * @return TeamProgramParticipation[]
     */
    public function showAll(string $teamId, int $page, int $pageSize)
    {
        return $this->teamProgramParticipationRepository->allTeamProgramParticipationsBelongsToTeam($teamId, $page,
                        $pageSize);
    }

    public function showById(string $teamId, string $teamProgramParticipationId): TeamProgramParticipation
    {
        return $this->teamProgramParticipationRepository->aTeamProgramParticipationBelongsToTeam($teamId,
                        $teamProgramParticipationId);
    }

}
