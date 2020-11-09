<?php

namespace Query\Application\Service\Firm\Manager;

use Query\Domain\Model\Firm\Manager\ManagerInvitation;

interface ManagerInvitationRepository
{

    public function anInvitationForManager(string $firmId, string $managerId, string $invitationId): ManagerInvitation;

    public function allInvitationsForManager(string $firmId, string $managerId, int $page, int $pageSize);
}
