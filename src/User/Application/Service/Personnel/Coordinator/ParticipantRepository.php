<?php

namespace User\Application\Service\Personnel\Coordinator;

use User\Domain\DependencyModel\Firm\Program\Participant;

interface ParticipantRepository
{

    public function ofId(string $participantId): Participant;
}
