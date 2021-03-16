<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Domain\Model\Firm\Client\ClientParticipant;

interface ClientParticipantRepository
{
    public function aClientParticipant(string $firmId, string $clientId, string $participantId): ClientParticipant;
}
