<?php

namespace Participant\Application\Service;

use Participant\Domain\Model\UserParticipant;

interface UserParticipantRepository
{

    public function ofId(string $userId, string $programParticipationId): UserParticipant;

    public function update(): void;
}
