<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\User\UserParticipant;

interface ProgramParticipationRepository
{

    public function ofId(string $userId, string $userParticipantId): UserParticipant;

    public function all(string $userId, int $page, int $pageSize);
}
