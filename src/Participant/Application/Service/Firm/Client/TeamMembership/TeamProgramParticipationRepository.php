<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership;

use Participant\Domain\Model\TeamProgramParticipation;

interface TeamProgramParticipationRepository
{

    public function ofId(string $teamProgramParticipationId): TeamProgramParticipation;

    public function update(): void;
}
