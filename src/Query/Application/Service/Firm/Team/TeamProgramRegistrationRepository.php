<?php

namespace Query\Application\Service\Firm\Team;

use Query\ {
    Application\Service\Firm\Client\TeamMembership\TeamProgramRegistrationRepository as InterfaceForClient,
    Domain\Model\Firm\Team\TeamProgramRegistration
};

interface TeamProgramRegistrationRepository extends InterfaceForClient
{

    public function ofId(string $firmId, string $teamId, string $teamProgramRegistrationId): TeamProgramRegistration;

    public function all(string $firmId, string $teamId, int $page, int $pageSize);
}
