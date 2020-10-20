<?php

namespace Query\Application\Service\Firm\Client\TeamMembership;

use Query\Domain\Model\Firm\Team\TeamProgramRegistration;

interface TeamProgramRegistrationRepository
{

    public function aTeamProgramRegistrationOfTeamWhereClientIsMember(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramRegistrationId): TeamProgramRegistration;

    public function allTeamProgramRegistrationsOfTeamWhereClientIsMember(
            string $firmId, string $clientId, string $teamMembershipId, int $page, int $pageSize);
}
