<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\Firm\Program\UserParticipant;

interface ProgramParticipationRepository
{

    public function aProgramParticipationOfUser(string $userId, string $firmId, string $programId): UserParticipant;

    public function allProgramParticipationsOfUser(string $userId, int $page, int $pageSize);
}
