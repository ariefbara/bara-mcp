<?php

namespace Query\Application\Service\Firm\Team;

use Query\Domain\Model\Firm\Team\TeamProgramParticipation;

interface TeamProgramParticipationRepository
{
    public function aTeamProgramParticipationBelongsToTeam(string $teamId, string $teamProgramParticipationId): TeamProgramParticipation;
    
    public function allTeamProgramParticipationsBelongsToTeam(string $teamId, int $page, int $pageSize, ?bool $activeStatus);
}
