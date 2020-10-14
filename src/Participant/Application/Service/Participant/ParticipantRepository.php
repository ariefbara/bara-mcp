<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant;

interface ParticipantRepository
{
    public function ofId(string $participantId): Participant;
}
