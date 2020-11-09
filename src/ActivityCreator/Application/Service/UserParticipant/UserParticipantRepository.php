<?php

namespace ActivityCreator\Application\Service\UserParticipant;

use ActivityCreator\Domain\DependencyModel\User\ProgramParticipation;

interface UserParticipantRepository
{
    public function aProgramParticipationBelongsToUser(string $userId, string $programParticipationId): ProgramParticipation;
}
