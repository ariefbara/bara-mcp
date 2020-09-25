<?php

namespace Query\Application\Service\Firm\Team;

use Query\Domain\Model\Firm\Team\TeamProgramParticipation;

interface TeamProgramParticipationRepository
{

    public function ofId(string $firmId, string $teamId, string $teamProgramParticipationId): TeamProgramParticipation;

    public function all(string $firmId, string $teamId, int $page, int $pageSize);
}
