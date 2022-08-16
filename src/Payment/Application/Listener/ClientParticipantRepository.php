<?php

namespace Payment\Application\Listener;

use Payment\Domain\Model\Firm\Client\ClientParticipant;

interface ClientParticipantRepository
{

    public function ofId(string $id): ?ClientParticipant;

    public function update(): void;
}
