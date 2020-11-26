<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\Domain\Model\Firm\Program\ClientParticipant;

interface ClientParticipantRepository
{
    public function aClientParticipantCorrespondWithProgram(string $firmId, string $clientId, string $programId): ClientParticipant;
}
