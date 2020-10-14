<?php

namespace Query\Domain\Service;

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

    public function execute(Team $team, string $programId): TeamProgramParticipation
    {
        return $this->teamProgramParticipationRepository
                        ->aTeamProgramParticipationCorrespondWithProgram($team->getId(), $programId);
    }

}
