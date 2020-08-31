<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\ClientParticipant;

interface ClientParticipantRepository
{
    public function ofId(string $firmId, string $programId, string $clientId): ClientParticipant;
}
