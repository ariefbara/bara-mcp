<?php

namespace Query\Application\Service\Firm\Personnel\ProgramCoordinator;

use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorInvitation;

interface CoordinatorInvitationRepository
{

    public function anInvitationForCoordinator(string $firmId, string $personnelId, string $invitationId): CoordinatorInvitation;

    public function allInvitationsForCoordinator(
            string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize);
}
