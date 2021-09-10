<?php

namespace Query\Application\Service\Client\TeamMember;

use Query\Domain\Model\Firm\Team\TeamProgramParticipation;

interface TeamParticipantRepository
{
    public function ofId(string $programParticipationId): TeamProgramParticipation;
}
