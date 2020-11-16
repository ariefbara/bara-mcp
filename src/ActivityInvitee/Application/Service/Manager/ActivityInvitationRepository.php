<?php

namespace ActivityInvitee\Application\Service\Manager;

use ActivityInvitee\Domain\Model\ManagerInvitee;

interface ActivityInvitationRepository
{

    public function anInvitationBelongsToManager(string $firmId, string $managerId, string $invitationId): ManagerInvitee;

    public function update(): void;
}
