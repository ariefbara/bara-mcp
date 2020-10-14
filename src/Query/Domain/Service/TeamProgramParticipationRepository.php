<?php

namespace Query\Domain\Service;

use Query\Domain\Model\Firm\Team\TeamProgramParticipation;

interface TeamProgramParticipationRepository
{
    public function aTeamProgramParticipationCorrespondWithProgram(string $teamId, string $programId): TeamProgramParticipation;
}
