<?php

namespace Query\Domain\Service\Firm\Team;

use Query\Domain\Model\Firm\Team\TeamProgramRegistration;

interface TeamProgramRegistrationRepository
{

    public function aProgramRegistrationOfTeam(string $teamId, string $teamProgramRegistrationId): TeamProgramRegistration;

    public function allProgramRegistrationsOfTeam(string $teamId, int $page, int $pageSize, ?bool $concludedStatus);
}
