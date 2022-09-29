<?php

namespace Query\Domain\Task\Dependency\User;

use Query\Domain\Model\User\UserParticipant;

interface UserParticipantRepository
{
    public function aUserParticipantInProgram(string $programId, string $id): UserParticipant;
}
