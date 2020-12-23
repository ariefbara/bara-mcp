<?php

namespace Participant\Application\Service\User;

use Participant\Domain\Model\UserParticipant;

interface UserParticipantRepository
{

    public function aUserParticipant(string $userId, string $programParticipationId): UserParticipant;

    public function update(): void;
}
