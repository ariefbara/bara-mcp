<?php

namespace User\Application\Listener;

use User\Domain\Model\User\ProgramParticipation;

interface ProgramParticipationRepository
{

    public function aParticipantOfProgram(string $firmId, string $programId, string $participantId): ProgramParticipation;
}
