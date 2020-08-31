<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\UserParticipant;

interface UserParticipantRepository
{

    public function ofId(string $firmId, string $programId, string $userId): UserParticipant;
}
