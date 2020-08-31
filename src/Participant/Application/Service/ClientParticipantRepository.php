<?php

namespace Participant\Application\Service;

use Participant\Domain\Model\ClientParticipant;

interface ClientParticipantRepository
{

    public function ofId(string $firmId, string $clientId, string $programParticipationId): ClientParticipant;

    public function update(): void;
}
