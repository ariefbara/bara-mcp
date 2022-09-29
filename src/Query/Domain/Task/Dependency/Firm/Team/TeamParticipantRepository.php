<?php

namespace Query\Domain\Task\Dependency\Firm\Team;

use Query\Domain\Model\Firm\Team\TeamProgramParticipation;

interface TeamParticipantRepository
{
    public function aTeamParticipantInProgram(string $programId, $id): TeamProgramParticipation;
}
