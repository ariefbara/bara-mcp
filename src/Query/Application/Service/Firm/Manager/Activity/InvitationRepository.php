<?php

namespace Query\Application\Service\Firm\Manager\Activity;

use Query\Domain\Model\Firm\Program\Activity\Invitation;

interface InvitationRepository
{

    public function anInvitationFromManager(string $firmId, string $managerId, string $invitationId): Invitation;

    public function allInvitationInManagerActivity(
            string $firmId, string $managerId, string $activityId, int $page, int $pageSize);
}
