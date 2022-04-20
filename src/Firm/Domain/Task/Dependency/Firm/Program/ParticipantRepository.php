<?php

namespace Firm\Domain\Task\Dependency\Firm\Program;

use Firm\Domain\Model\Firm\Program\Participant;

interface ParticipantRepository
{

    public function ofId(string $participantId): Participant;
}
