<?php

namespace Client\Application\Listener;

use Client\Domain\Model\Client\ProgramParticipation;

interface ProgramParticipationRepository
{

    public function aParticipantOfProgram(string $firmId, string $programId, string $participantId): ProgramParticipation;
}
