<?php

namespace ActivityCreator\Application\Service\ClientParticipant;

use ActivityCreator\Domain\DependencyModel\Firm\Client\ProgramParticipation;

interface ClientParticipantRepository
{
    public function aProgramParticipationBelongsToClient(string $firmId, string $clientId, string $programParticipationId): ProgramParticipation;
}
