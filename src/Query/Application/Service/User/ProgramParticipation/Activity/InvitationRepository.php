<?php

namespace Query\Application\Service\User\ProgramParticipation\Activity;

use Query\Domain\Model\Firm\Program\Activity\Invitee;

interface InvitationRepository
{

    public function anInvitationFromUser(string $userId, string $invitationId): Invitee;

    public function allInvitationsInUserParticipantActivity(
            string $userId, string $activityId, int $page, int $pageSize);
}
