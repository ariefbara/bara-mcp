<?php

namespace ActivityInvitee\Application\Service\Coordinator;

use ActivityInvitee\Domain\Model\CoordinatorInvitee;

interface ActivityInvitationRepository
{
    public function anInvitationBelongsToPersonnel(string $firmId, string $personnelId, string $invitationId): CoordinatorInvitee;
    
    public function update(): void;
}
