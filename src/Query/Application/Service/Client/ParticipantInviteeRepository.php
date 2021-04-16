<?php

namespace Query\Application\Service\Client;

use Query\Infrastructure\QueryFilter\InviteeFilter;

interface ParticipantInviteeRepository
{

    public function allAccessibleParticipantInviteeBelongsToClient(
            string $clientId, int $page, int $pageSize, ?InviteeFilter $inviteeFilter);
}
