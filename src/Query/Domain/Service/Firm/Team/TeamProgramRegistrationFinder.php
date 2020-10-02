<?php

namespace Query\Domain\Service\Firm\Team;

use Query\Domain\Model\Firm\{
    Team,
    Team\TeamProgramRegistration
};

class TeamProgramRegistrationFinder
{

    /**
     *
     * @var TeamProgramRegistrationRepository
     */
    protected $teamProgramRegistrationRepository;

    public function __construct(TeamProgramRegistrationRepository $teamProgramRegistrationRepository)
    {
        $this->teamProgramRegistrationRepository = $teamProgramRegistrationRepository;
    }

    public function findProgramRegistrationBelongsToTeam(Team $team, string $teamProgramRegistrationId): TeamProgramRegistration
    {
        $teamId = $team->getId();
        return $this->teamProgramRegistrationRepository->aProgramRegistrationOfTeam($teamId, $teamProgramRegistrationId);
    }

    public function findAllProgramRegistrationsBelongsToTeam(Team $team, int $page, int $pageSize,
            ?bool $concludedStatus)
    {
        $teamId = $team->getId();
        return $this->teamProgramRegistrationRepository
                        ->allProgramRegistrationsOfTeam($teamId, $page, $pageSize, $concludedStatus);
    }

}
