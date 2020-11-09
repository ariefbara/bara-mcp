<?php

namespace Query\Application\Service\Firm\Personnel\ProgramCoordinator\Activity;

use Query\Domain\Model\Firm\Program\Activity\Invitation;

interface InvitationRepository
{

    public function anInvitationFromCoordinator(string $firmId, string $personnelId, string $invitationId): Invitation;

    public function allInvitationsInCoordinatorActivity(
            string $firmId, string $personnelId, string $activityId, int $page, int $pageSize);
}
