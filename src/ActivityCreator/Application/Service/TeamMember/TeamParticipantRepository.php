<?php

namespace ActivityCreator\Application\Service\TeamMember;

use ActivityCreator\Domain\DependencyModel\Firm\Team\ProgramParticipation;

interface TeamParticipantRepository
{
    public function ofId(string $programParticipationId): ProgramParticipation;
}
