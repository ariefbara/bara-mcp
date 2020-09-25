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
     * @param string $firmId
     * @param string $teamId
     * @param int $page
     * @param int $pageSize
     * @return TeamProgramParticipation[]
     */
    public function showAll(string $firmId, string $teamId, int $page, int $pageSize)
    {
        return $this->teamProgramParticipationRepository->all($firmId, $teamId, $page, $pageSize);
    }

    public function showById(string $firmId, string $teamId, string $teamProgramParticipationId): TeamProgramParticipation
    {
        return $this->teamProgramParticipationRepository->ofId($firmId, $teamId, $teamProgramParticipationId);
    }

}
