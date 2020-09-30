<?php

namespace Query\Domain\Service\Firm\Team;

use Query\Domain\Model\Firm\Team\TeamProgramParticipation;

interface TeamProgramParticipationRepository
{

    public function ofId(string $teamId, string $teamProgramParticipationId): TeamProgramParticipation;

    public function all(string $teamId, int $page, int $pageSize);
}
