<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Domain\Model\TeamProgramParticipation;

interface TeamParticipantRepository
{
    public function ofId(string $teamProgramParticipationId): TeamProgramParticipation;
    
    public function update(): void;
}
