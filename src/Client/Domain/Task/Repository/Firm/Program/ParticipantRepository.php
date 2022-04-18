<?php

namespace Client\Domain\Task\Repository\Firm\Program;

use Client\Domain\DependencyModel\Firm\Program\Participant;

interface ParticipantRepository
{

    public function ofId(string $id): Participant;
}
