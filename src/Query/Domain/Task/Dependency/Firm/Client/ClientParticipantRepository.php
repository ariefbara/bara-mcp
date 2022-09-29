<?php

namespace Query\Domain\Task\Dependency\Firm\Client;

use Query\Domain\Model\Firm\Client\ClientParticipant;

interface ClientParticipantRepository
{

    public function aClientParticipantInProgram(string $programId, string $id): ClientParticipant;
}
