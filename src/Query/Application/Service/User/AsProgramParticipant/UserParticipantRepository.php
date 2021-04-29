<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\Domain\Model\User\UserParticipant;

interface UserParticipantRepository
{
    public function aUserParticipant(string $userId, string $participantId): UserParticipant;
    
    public function aProgramParticipationOfUserCorrespondWithProgram(string $userId, string $programId): UserParticipant;
}
