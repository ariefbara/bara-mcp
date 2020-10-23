<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Participant;

interface ParticipantRepository
{

    public function ofId(string $participantId): Participant;

    public function update(): void;
}
