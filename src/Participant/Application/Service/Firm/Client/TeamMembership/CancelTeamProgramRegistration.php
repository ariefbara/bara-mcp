<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership;

use Participant\Application\Service\ {
    Firm\Client\TeamMembershipRepository,
    TeamProgramRegistrationRepository
};

class CancelTeamProgramRegistration
{

    /**
     *
     * @var TeamProgramRegistrationRepository
     */
    protected $teamProgramRegistrationRepository;

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    public function __construct(TeamProgramRegistrationRepository $teamProgramRegistrationRepository,
            TeamMembershipRepository $teamMembershipRepository)
    {
        $this->teamProgramRegistrationRepository = $teamProgramRegistrationRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramRegistrationId): void
    {
        $teamProgramRegistration = $this->teamProgramRegistrationRepository->ofId($teamProgramRegistrationId);
        $this->teamMembershipRepository->ofId($firmId, $clientId, $teamMembershipId)
                ->cancelTeamprogramRegistration($teamProgramRegistration);
        $this->teamProgramRegistrationRepository->update();
    }

}
