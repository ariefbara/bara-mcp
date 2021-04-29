<?php

namespace Query\Application\Service\Firm\Client\AsTeamMember\AsProgramParticipant;

use Query\Domain\Model\Firm\Team\TeamProgramParticipation;

interface TeamProgramParticipationRepository
{
    public function ofId(string $programParticipationId): TeamProgramParticipation;
    
    public function aTeamProgramParticipationCorrespondWithProgram(string $teamId, string $programId): TeamProgramParticipation;
}
