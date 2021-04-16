<?php

namespace Query\Application\Service\User;

use Query\Infrastructure\QueryFilter\InviteeFilter;

interface ParticipantInviteeRepository
{

    public function allParticipantInviteeBelongsToUser(
            string $userId, int $page, int $pageSize, ?InviteeFilter $inviteeFilter);
}
