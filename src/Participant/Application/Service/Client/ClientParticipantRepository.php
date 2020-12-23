<?php

namespace Participant\Application\Service\Client;

use Participant\Domain\Model\ClientParticipant;

interface ClientParticipantRepository
{

    public function aClientParticipant(string $firmId, string $clientId, string $programParticipationId): ClientParticipant;

    public function update(): void;
}
