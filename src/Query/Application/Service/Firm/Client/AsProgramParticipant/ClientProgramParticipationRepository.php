<?php

namespace Query\Application\Service\Firm\Client\AsProgramParticipant;

use Query\Domain\Model\Firm\Client\ClientParticipant;

interface ClientProgramParticipationRepository
{
    public function aClientProgramParticipationCorrespondWithProgram(string $clientId, string $programId): ClientParticipant;
}
