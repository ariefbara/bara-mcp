<?php

namespace Query\Application\Service\Firm\Client\TeamMembership;

use Query\Domain\Model\Firm\Team\TeamProgramRegistration;

class ViewTeamProgramRegistration
{

    /**
     *
     * @var TeamProgramRegistrationRepository
     */
    protected $teamProgramRegistrationRepository;

    /**
     *
     * @var ActiveTeamMembershipAuthorization
     */
    protected $activeTeamMemberAuthorization;

    public function __construct(TeamProgramRegistrationRepository $teamProgramRegistrationRepository,
            ActiveTeamMembershipAuthorization $activeTeamMemberAuthorization)
    {
        $this->teamProgramRegistrationRepository = $teamProgramRegistrationRepository;
        $this->activeTeamMemberAuthorization = $activeTeamMemberAuthorization;
    }

    public function showAll(string $firmId, string $clientId, string $teamMembershipId, int $page, int $pageSize)
    {
        $this->activeTeamMemberAuthorization->execute($firmId, $clientId, $teamMembershipId);
        return $this->teamProgramRegistrationRepository
                        ->allTeamProgramRegistrationsOfTeamWhereClientIsMember($firmId, $clientId, $teamMembershipId,
                                $page, $pageSize);
    }

    public function showById(string $firmId, string $clientId, string $teamMembershipId,
            string $teamProgramRegistrationId): TeamProgramRegistration
    {
        $this->activeTeamMemberAuthorization->execute($firmId, $clientId, $teamMembershipId);
        return $this->teamProgramRegistrationRepository
                ->aTeamProgramRegistrationOfTeamWhereClientIsMember($firmId, $clientId, $teamMembershipId,
                        $teamProgramRegistrationId);
    }

}
