<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation\Activity;

use Query\Domain\Model\Firm\Program\Activity\Invitee;

interface InvitationRepository
{

    public function anInvitationFromClient(string $firmId, string $clientId, string $invitationId): Invitee;

    public function allInvitationsInClientParticipantActivity(
            string $firmId, string $clientId, string $activityId, int $page, int $pageSize);
}
