<?php

namespace Firm\Application\Service\User\ProgramParticipant;

use Firm\Domain\Model\Firm\Program\UserParticipant;

interface UserParticipantRepository
{

    public function aUserParticipantCorrespondWithProgram(string $userId, string $programId): UserParticipant;
    
    public function aUserParticipantBelongsToUser(string $userId, string $participantId): UserParticipant;
    
    public function update(): void;
}
