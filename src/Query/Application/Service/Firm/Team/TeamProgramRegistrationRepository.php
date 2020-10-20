<?php

namespace Query\Application\Service\Firm\Team;

use Query\ {
    Domain\Model\Firm\Team\TeamProgramRegistration
};

interface TeamProgramRegistrationRepository
{

    public function ofId(string $firmId, string $teamId, string $teamProgramRegistrationId): TeamProgramRegistration;

    public function all(string $firmId, string $teamId, int $page, int $pageSize, ?bool $concludedStatus);
}
