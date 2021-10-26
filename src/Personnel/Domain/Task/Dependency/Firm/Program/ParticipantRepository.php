<?php

namespace Personnel\Domain\Task\Dependency\Firm\Program;

use Personnel\Domain\Model\Firm\Program\Participant;

interface ParticipantRepository
{

    public function ofId(string $id): Participant;
}
