<?php

namespace Query\Application\Service\Firm\Personnel\ProgramCoordinator;

use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;

interface CoordinatorInvitationRepository
{

    public function anInvitationForCoordinator(string $firmId, string $personnelId, string $invitationId): CoordinatorInvitee;

    public function allInvitationsForCoordinator(
            string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize, 
            ?InviteeFilter $inviteeFilter);
}
