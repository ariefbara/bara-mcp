<?php

namespace Query\Application\Service\Firm\Manager;

use Query\Domain\Model\Firm\Manager\ManagerInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;

interface ManagerInvitationRepository
{

    public function anInvitationForManager(string $firmId, string $managerId, string $invitationId): ManagerInvitee;

    public function allInvitationsForManager(
            string $firmId, string $managerId, int $page, int $pageSize, ?InviteeFilter $inviteeFilter);
}
